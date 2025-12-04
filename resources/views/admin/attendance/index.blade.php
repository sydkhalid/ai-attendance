@extends('adminlte::page')

@section('title', 'Attendance Logs')

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css">
@stop

@section('content_header')
    <h1>Attendance Logs</h1>
@stop

@section('content')

<div class="card">
    <div class="card-body">

        {{-- FILTER ROW --}}
        <div class="row mb-3">
            <div class="col-md-3">
                <input type="date" id="filter_date" class="form-control" value="{{ $date }}">
            </div>

            <div class="col-md-2">
                <button id="btnFilter" class="btn btn-primary">Filter</button>
            </div>

            <div class="col-md-2">
                <a id="exportBtn" href="{{ route('admin.attendance.export', ['date' => $date]) }}"
                   class="btn btn-success">Export CSV</a>
            </div>
        </div>


        {{-- DATATABLE --}}
        <table id="attendanceTable" class="table table-bordered table-striped w-100">
            <thead class="table-dark">
                <tr>
                    <th>Student</th>
                    <th>Roll No</th>
                    <th>Status</th>
                    <th>Date</th>
                    {{-- <th>Image</th> --}}
                </tr>
            </thead>
        </table>

    </div>
</div>

@stop


@section('js')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>


<script>

$(function () {

    // DATATABLE LOAD
    let table = $('#attendanceTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.attendance.ajax') }}",
            data: function(d) {
                d.date = $("#filter_date").val();
            }
        },
        columns: [
            { data: 'student_name' },
            { data: 'roll_no' },
            { 
                data: 'status',
                render: function(data) {
                    return `<span class="badge bg-${data === 'present' ? 'success' : 'danger'}">${data}</span>`;
                }
            },
            { data: 'attendance_date' },
            // { 
            //     data: 'image_url', 
            //     orderable: false, 
            //     searchable: false,
            //     render: function(path) {
            //         if (!path) return "-";
            //         return `<img src="/storage/${path}" width="60" class="rounded">`;
            //     }
            // }
        ]
    });

    // FILTER BUTTON
    $("#btnFilter").on("click", function () {
        table.ajax.reload();
        
        // update export button link
        let d = $("#filter_date").val();
        $("#exportBtn").attr("href", "/admin/attendance/export?date=" + d);
    });

});

</script>

@stop
