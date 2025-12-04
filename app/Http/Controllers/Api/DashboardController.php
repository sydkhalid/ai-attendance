<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AttendanceLog;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->format('Y-m-d');

        // Use attendance_date instead of created_at
        $present = AttendanceLog::whereDate('attendance_date', $today)
            ->where('status', 'present')
            ->count();

        $absent = AttendanceLog::whereDate('attendance_date', $today)
            ->where('status', 'absent')
            ->count();

        // Total students marked today
        $totalUsers = AttendanceLog::whereDate('attendance_date', $today)
            ->distinct()
            ->count('student_id');

        return response()->json([
            'success' => true,
            'present' => $present,
            'absent'  => $absent,
            'total'   => $totalUsers,
        ]);
    }
}
