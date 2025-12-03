<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use App\Services\AwsRekognitionService;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    public function store(Request $request, AwsRekognitionService $rekognition)
    {
        $request->validate([
            'name' => 'required',
            'roll_no' => 'nullable|string',
            'face_image' => 'required|image'
        ]);

        // Save student first
        $student = Student::create([
            'name' => $request->name,
            'roll_no' => $request->roll_no
        ]);

        // Upload image locally
        $path = $request->file('face_image')->store('students', 'public');

        // Read image bytes
        $imageBytes = file_get_contents(storage_path('app/public/' . $path));

        // AWS Rekognition → Index face
        $faceId = $rekognition->indexFace($imageBytes);

        // Save Face ID + image path
        $student->update([
            'face_image' => $path,
            'rekognition_face_id' => $faceId
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Student created & face indexed successfully!',
            'data' => $student
        ]);
    }
}
