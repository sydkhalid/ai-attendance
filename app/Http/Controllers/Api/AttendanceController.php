<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\Student;
use App\Models\StudentImage;
use App\Services\AwsRekognitionService;
use Illuminate\Http\Request;

// Correct for Intervention Image v3
use Intervention\Image\Laravel\Facades\Image;

class AttendanceController extends Controller
{
    public function detect(Request $request, AwsRekognitionService $rekognition)
    {
        $request->validate([
            'image' => 'required|image|max:8192'
        ]);

        $image = $request->file('image');
        $bytes = file_get_contents($image->getRealPath());

        // 1. Detect faces in the full image
        $detected = $rekognition->detectFaces($bytes);

        if (empty($detected['FaceDetails'])) {
            return response()->json([
                "present_students" => [],
                "absent_students" => Student::all(),
                "error" => "No faces detected"
            ], 200);
        }

        $presentStudentIds = [];

        foreach ($detected['FaceDetails'] as $face) {

            // 2. Crop each face
            $cropBytes = $this->extractFace($image, $face['BoundingBox']);

            // 3. Search cropped face in AWS Rekognition
            $matches = $rekognition->searchFaces($cropBytes);

            foreach ($matches as $match) {
                $faceId = $match['Face']['FaceId'];

                $studentImg = StudentImage::where('rekognition_face_id', $faceId)->first();

                if ($studentImg) {
                    $presentStudentIds[] = $studentImg->student_id;
                }
            }
        }

        $presentStudentIds = array_unique($presentStudentIds);

        $presentStudents = Student::whereIn('id', $presentStudentIds)->get();
        $absentStudents = Student::whereNotIn('id', $presentStudentIds)->get();

        return response()->json([
            "present_students" => $presentStudents,
            "absent_students"  => $absentStudents,
            "image_url" => null
        ], 200);
    }


    /**
     * Crop detected face using Intervention Image v3
     */
    private function extractFace($imageFile, $box)
    {
        // V3 uses Image::read()
        $img = Image::read($imageFile->getRealPath());

        // Convert AWS bounding box to pixel values
        $width  = $img->width();
        $height = $img->height();

        $w = (int) ($width  * $box['Width']);
        $h = (int) ($height * $box['Height']);
        $x = (int) ($width  * $box['Left']);
        $y = (int) ($height * $box['Top']);

        // Crop only the face
        $crop = $img->crop($w, $h, $x, $y);

        // Convert to JPEG bytes (AWS compatible)
        return (string) $crop->toJpeg(90);
    }



    /**
     * Save attendance (submit)
     */
    public function submit(Request $request)
    {
        $request->validate([
            'present_student_ids'   => 'required|array',
            'present_student_ids.*' => 'integer|exists:students,id',
            'image_url'             => 'nullable|string'
        ]);

        $today = now()->toDateString();

        // All students
        $allStudents = Student::pluck('id')->toArray();

        $present = $request->present_student_ids;
        $absent  = array_diff($allStudents, $present);

        // Save PRESENT students
        foreach ($present as $studentId) {
            AttendanceLog::updateOrCreate(
                [
                    'student_id'      => $studentId,
                    'attendance_date' => $today
                ],
                [
                    'status'          => 'present',
                    'image_url'       => $request->image_url,
                ]
            );
        }

        // Save ABSENT students
        foreach ($absent as $studentId) {
            AttendanceLog::updateOrCreate(
                [
                    'student_id'      => $studentId,
                    'attendance_date' => $today
                ],
                [
                    'status'          => 'absent',
                    'image_url'       => $request->image_url,
                ]
            );
        }

        return response()->json([
            "message" => "Attendance submitted successfully",
            "present" => $present,
            "absent"  => $absent
        ], 200);
    }
}
