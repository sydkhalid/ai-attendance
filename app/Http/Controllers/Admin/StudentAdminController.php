<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentImage;
use App\Services\AwsRekognitionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentAdminController extends Controller
{
    /**
     * LIST + DATATABLE
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = Student::with('images')->orderBy('id', 'DESC');

            return datatables()->eloquent($query)
                ->addColumn('image', function ($s) {
                    if ($s->face_image) {
                        return '<img src="' . asset('storage/' . $s->face_image)
                            . '" width="50" class="img-thumbnail rounded">';
                    }
                    return '<span class="badge bg-secondary">No Image</span>';
                })
                ->addColumn('actions', function ($s) {
                    return '
                        <button data-id="' . $s->id . '" class="btn btn-info btn-sm viewBtn">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button data-id="' . $s->id . '" class="btn btn-warning btn-sm editBtn">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button data-id="' . $s->id . '" class="btn btn-danger btn-sm deleteBtn">
                            <i class="fas fa-trash"></i>
                        </button>
                    ';
                })
                ->rawColumns(['image', 'actions'])
                ->make(true);
        }

        return view('admin.students.index');
    }


    /**
     * STORE — MULTIPLE FACE INDEXING
     */
    public function store(Request $request, AwsRekognitionService $rekognition)
    {
        $request->validate([
            'name' => 'required|string|unique:students,name',
            'roll_no' => 'nullable|string|unique:students,roll_no',
            'face_images' => 'required|array',
            'face_images.*' => 'image|max:4096'
        ]);

        $student = Student::create([
            'name' => $request->name,
            'roll_no' => $request->roll_no
        ]);

        $primaryImage = null;
        $primaryFaceId = null;

        foreach ($request->file('face_images') as $index => $file) {

            $path = $file->store('students', 'public');
            $bytes = file_get_contents(storage_path("app/public/$path"));

            $faceId = $rekognition->indexFace($bytes, $student->id);

            StudentImage::create([
                'student_id' => $student->id,
                'image_path' => $path,
                'rekognition_face_id' => $faceId
            ]);

            if ($index == 0) {
                $primaryImage = $path;
                $primaryFaceId = $faceId;
            }
        }

        $student->update([
            'face_image' => $primaryImage,
            'rekognition_face_id' => $primaryFaceId
        ]);

        return response()->json(['status' => true, 'message' => 'Added']);
    }


    /**
     * SHOW ONE STUDENT
     */
    public function show($id)
    {
        return Student::with('images')->findOrFail($id);
    }


    /**
     * UPDATE — ADD MORE IMAGES ONLY (B2 MODE)
     */
    public function update(Request $request, AwsRekognitionService $rekognition, $id)
    {
        $student = Student::findOrFail($id);

        $request->validate([
            'name' => "required|string|unique:students,name,$id",
            'roll_no' => "nullable|string|unique:students,roll_no,$id",
            'face_images.*' => 'image|max:4096'
        ]);

        $student->update([
            'name' => $request->name,
            'roll_no' => $request->roll_no,
        ]);

        if ($request->hasFile('face_images')) {

            foreach ($request->file('face_images') as $file) {

                $path = $file->store('students', 'public');
                $bytes = file_get_contents(storage_path("app/public/$path"));

                $faceId = $rekognition->indexFace($bytes, $student->id);

                StudentImage::create([
                    'student_id' => $student->id,
                    'image_path' => $path,
                    'rekognition_face_id' => $faceId
                ]);
            }
        }

        return response()->json(['status' => true, 'message' => 'Updated']);
    }


    /**
     * DELETE WHOLE STUDENT
     */
    public function destroy($id, AwsRekognitionService $rekognition)
    {
        $student = Student::with('images')->findOrFail($id);

        foreach ($student->images as $img) {
            if ($img->rekognition_face_id) {
                $rekognition->deleteFace($img->rekognition_face_id);
            }
            Storage::disk('public')->delete($img->image_path);
        }

        $student->images()->delete();
        $student->delete();

        return response()->json(['status' => true]);
    }


    /**
     * DELETE SINGLE IMAGE (B2 FEATURE)
     */
    public function deleteImage(Request $request, AwsRekognitionService $rekognition)
    {
        $img = StudentImage::findOrFail($request->id);

        // Delete face from AWS
        if ($img->rekognition_face_id) {
            $rekognition->deleteFace($img->rekognition_face_id);
        }

        Storage::disk('public')->delete($img->image_path);
        $img->delete();

        return response()->json(['status' => true]);
    }
}
