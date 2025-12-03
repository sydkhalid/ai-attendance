<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentImage extends Model
{
    protected $fillable = [
        'student_id', 'image_path', 'rekognition_face_id'
    ];
}
