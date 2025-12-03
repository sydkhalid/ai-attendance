<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentImage;
use App\Services\AwsRekognitionService;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function detect(Request $request, AwsRekognitionService $rekognition)
    {
        $request->validate([
            'image' => 'required|image|max:8192'
        ]);

        // Convert group photo to bytes
        $bytes = file_get_contents($request->file('image')->getRealPath());

        // Search in Rekognition
        $matches = $rekognition->searchFaces($bytes);

        $presentStudentIds = [];

        foreach ($matches as $match) {

            $faceId = $match['Face']['FaceId'];

            // Find matching student
            $studentImg = StudentImage::where('rekognition_face_id', $faceId)->first();

            if ($studentImg) {
                $presentStudentIds[] = $studentImg->student_id;
            }
        }

        $presentStudents = Student::whereIn('id', $presentStudentIds)->get();
        $absentStudents = Student::whereNotIn('id', $presentStudentIds)->get();

        return response()->json([
            "present_students" => $presentStudents,
            "absent_students" => $absentStudents
        ]);
    }
}
