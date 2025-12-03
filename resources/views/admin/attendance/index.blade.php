@extends('adminlte::page')

@section('title', 'Attendance Logs')

@section('content_header')
    <h1>Attendance Logs</h1>
@stop

@section('content')

    <form method="GET" action="{{ route('admin.attendance.index') }}">
        <div class="row mb-3">
            <div class="col-md-3">
                <input type="date" name="date" class="form-control" value="{{ $date }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary">Filter</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.attendance.export', ['date' => $date]) }}"
                   class="btn btn-success">Export CSV</a>
            </div>
        </div>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Student</th>
                <th>Roll No</th>
                <th>Status</th>
                <th>Date</th>
                <th>Image</th>
            </tr>
        </thead>
        <tbody>

        @foreach($attendance as $a)
            <tr>
                <td>{{ $a->student->name }}</td>
                <td>{{ $a->student->roll_no }}</td>
                <td>
                    <span class="badge bg-success">{{ $a->status }}</span>
                </td>
                <td>{{ $a->attendance_date }}</td>
                <td>
                    @if($a->image_url)
                        <img src="{{ asset('storage/' . $a->image_url) }}" width="60">
                    @endif
                </td>
            </tr>
        @endforeach

        </tbody>
    </table>

@stop
