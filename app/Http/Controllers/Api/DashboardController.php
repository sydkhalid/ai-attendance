<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AttendanceLog;

class DashboardController extends Controller
{
    public function stats()
    {
        $today = now()->format('Y-m-d');

        $present = AttendanceLog::whereDate('created_at', $today)
            ->where('status', 'present')
            ->count();

        $absent = AttendanceLog::whereDate('created_at', $today)
            ->where('status', 'absent')
            ->count();

        $totalUsers = AttendanceLog::distinct()->count('student_id');

        return response()->json([
            'success' => true,
            'present' => $present,
            'absent'  => $absent,
            'total'   => $totalUsers,
        ]);
    }
}
