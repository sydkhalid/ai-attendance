<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use Illuminate\Http\Request;

class AttendanceAdminController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->date ?? today()->format('Y-m-d');

        $attendance = AttendanceLog::with('student')
            ->whereDate('attendance_date', $date)
            ->orderBy('id', 'DESC')
            ->get();

        return view('admin.attendance.index', compact('attendance', 'date'));
    }

    public function export(Request $request)
    {
        $date = $request->date ?? today()->format('Y-m-d');

        $attendance = AttendanceLog::with('student')
            ->whereDate('attendance_date', $date)
            ->get();

        $csv = "Student,Roll No,Status,Date\n";

        foreach ($attendance as $row) {
            $csv .= "{$row->student->name},{$row->student->roll_no},{$row->status},{$row->attendance_date}\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename=attendance_'.$date.'.csv');
    }
}
