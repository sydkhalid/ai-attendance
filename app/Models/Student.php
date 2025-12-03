<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'name',
        'roll_no',
        'face_image',
        'rekognition_face_id',
    ];

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function images()
    {
        return $this->hasMany(StudentImage::class);
    }

}
