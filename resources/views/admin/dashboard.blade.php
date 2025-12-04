@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1 class="mb-2">Dashboard</h1>
@stop

@section('content')

<div class="row">

    <!-- TOTAL STUDENTS -->
    <div class="col-md-4">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ $totalStudents }}</h3>
                <p>Total Students</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>

    <!-- PRESENT TODAY -->
    <div class="col-md-4">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $todayPresent }}</h3>
                <p>Present Today</p>
            </div>
            <div class="icon">
                <i class="fas fa-user-check"></i>
            </div>
        </div>
    </div>

    <!-- ABSENT TODAY -->
    <div class="col-md-4">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $todayAbsent }}</h3>
                <p>Absent Today</p>
            </div>
            <div class="icon">
                <i class="fas fa-user-times"></i>
            </div>
        </div>
    </div>

</div>



<!-- RECENT ATTENDANCE -->
<div class="card mt-4">
    <div class="card-header">
        <h4 class="card-title">Recent Attendance Logs</h4>
    </div>

    <div class="card-body p-0">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Roll No</th>
                    <th>Status</th>
                    <th>Date</th>
                    {{-- <th>Image</th> --}}
                </tr>
            </thead>

            <tbody>
                @foreach($recentAttendance as $a)
                <tr>
                    <td>{{ $a->student->name }}</td>
                    <td>{{ $a->student->roll_no }}</td>

                    <td>
                        @if($a->status === 'present')
                            <span class="badge bg-success">Present</span>
                        @else
                            <span class="badge bg-danger">Absent</span>
                        @endif
                    </td>

                    <td>{{ $a->attendance_date }}</td>
{{-- 
                    <td>
                        @if($a->image_url)
                            <img src="{{ asset('storage/'.$a->image_url) }}" width="40" class="img-thumbnail">
                        @endif
                    </td> --}}
                </tr>
                @endforeach
            </tbody>

        </table>
    </div>
</div>

@stop
