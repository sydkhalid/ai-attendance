<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\AttendanceLog;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();

        return view('admin.dashboard', [
            'totalStudents'   => Student::count(),
            'todayPresent'    => AttendanceLog::where('attendance_date', $today)->where('status', 'present')->count(),
            'todayAbsent'     => AttendanceLog::where('attendance_date', $today)->where('status', 'absent')->count(),
            'recentAttendance'=> AttendanceLog::with('student')
                                    ->orderBy('id', 'DESC')
                                    ->limit(10)
                                    ->get()
        ]);
    }
}
