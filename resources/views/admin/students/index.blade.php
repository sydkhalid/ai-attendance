@extends('adminlte::page')

@section('title', 'Students')

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<style>
    .image-box {
        position: relative;
        margin: 6px;
        display: inline-block;
    }

    .image-box img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 6px;
        border: 2px solid #ddd;
    }

    .delete-img-btn {
        position: absolute;
        top: -5px;
        right: -5px;
        font-size: 20px;
        cursor: pointer;
        color: #ff0033;
        opacity: 0.9;
        transition: 0.2s;
    }

    .delete-img-btn:hover {
        opacity: 1;
        transform: scale(1.15);
    }
</style>
@stop



@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="mb-1">Students</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Students</li>
        </ol>
    </div>

    <button class="btn btn-primary" data-toggle="modal" data-target="#studentModal">
        <i class="fas fa-plus-circle"></i> Add Student
    </button>
</div>
@stop




@section('content')

<div class="card">
    <div class="card-body">
        <table id="studentsTable" class="table table-bordered table-striped w-100">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Roll No</th>
                    <th>Image</th>
                    <th>Face ID</th>
                    <th class="no-export">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>




{{-- ADD STUDENT MODAL --}}
<div class="modal fade" id="studentModal">
    <div class="modal-dialog modal-lg">
        <form id="addStudentForm" action="/admin/students/store" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="modal-content">

                <div class="modal-header bg-primary text-white">
                    <h4 class="modal-title">Add Student</h4>
                    <button type="button" class="close text-white" data-dismiss="modal">×</button>
                </div>

                <div class="modal-body">

                    <div id="addFormErrors"></div>

                    <div class="form-group">
                        <label><b>Name:</b></label>
                        <input type="text" name="name" class="form-control">
                    </div>

                    <div class="form-group mt-2">
                        <label><b>Roll No:</b></label>
                        <input type="text" name="roll_no" class="form-control">
                    </div>

                    <div class="form-group mt-2">
                        <label><b>Upload Multiple Face Images:</b></label>
                        <input type="file" name="face_images[]" id="face_images" class="form-control" multiple>
                        <div id="previewContainer" class="mt-3 d-flex flex-wrap"></div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-success btnSubmit">
                        <i class="fas fa-save"></i> Save
                    </button>
                </div>

            </div>

        </form>
    </div>
</div>




{{-- VIEW STUDENT MODAL --}}
<div class="modal fade" id="viewStudentModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header bg-info text-white">
                <h4 class="modal-title">View Student</h4>
                <button class="close text-white" data-dismiss="modal">×</button>
            </div>

            <div class="modal-body">

                <h5><b>Name:</b> <span id="viewName"></span></h5>
                <h6><b>Roll No:</b> <span id="viewRoll"></span></h6>
                <hr>

                <h5>All Images</h5>
                <div id="viewImages" class="d-flex flex-wrap"></div>

            </div>

        </div>
    </div>
</div>




{{-- EDIT STUDENT MODAL --}}
<div class="modal fade" id="editStudentModal">
    <div class="modal-dialog modal-lg">
        <form id="editStudentForm" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">

                <div class="modal-header bg-warning">
                    <h4 class="modal-title">Edit Student</h4>
                    <button class="close" data-dismiss="modal">×</button>
                </div>

                <div class="modal-body">
                    <div id="editFormErrors"></div>

                    <input type="hidden" id="edit_id">

                    <div class="form-group">
                        <label><b>Name:</b></label>
                        <input type="text" id="edit_name" name="name" class="form-control">
                    </div>

                    <div class="form-group mt-2">
                        <label><b>Roll No:</b></label>
                        <input type="text" id="edit_roll" name="roll_no" class="form-control">
                    </div>

                    <hr>
                    <h5>Existing Images</h5>
                    <div id="editImages" class="d-flex flex-wrap"></div>

                    <div class="form-group mt-3">
                        <label><b>Add More Images:</b></label>
                        <input type="file" name="face_images[]" class="form-control" multiple>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-warning updateBtn">
                        <i class="fas fa-save"></i> Update
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

@stop




@section('js')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>


<script>
$(function () {

    // ------------------------------------------
    // DATATABLE
    // ------------------------------------------
    var table = $('#studentsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "/admin/students",
        dom: 'Bfrtip',
        buttons: [
            { extend: 'copy', className: 'btn btn-secondary btn-sm', exportOptions: { columns: ':not(.no-export)' } },
            { extend: 'excel', className: 'btn btn-success btn-sm', exportOptions: { columns: ':not(.no-export)' } },
            { extend: 'csv', className: 'btn btn-info btn-sm', exportOptions: { columns: ':not(.no-export)' } },
            { extend: 'pdf', className: 'btn btn-danger btn-sm', exportOptions: { columns: ':not(.no-export)' } },
            { extend: 'print', className: 'btn btn-dark btn-sm', exportOptions: { columns: ':not(.no-export)' } }
        ],
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'roll_no' },
            { data: 'image', orderable: false, searchable: false },
            {
                data: 'rekognition_face_id',
                render: function (data) {
                    return data ? data : '-';
                }
            },
            { data: 'actions', orderable: false, searchable: false, className: 'no-export' }
        ]
    });



    // ------------------------------------------
    // ADD — Image Preview
    // ------------------------------------------
    let selectedFiles = [];

    $("#face_images").on("change", function (e) {

        $("#previewContainer").html("");
        selectedFiles = Array.from(e.target.files);

        selectedFiles.forEach((file, index) => {
            let reader = new FileReader();
            reader.onload = (ev) => {
                $("#previewContainer").append(`
                    <div class="image-box">
                        <img src="${ev.target.result}">
                    </div>
                `);
            };
            reader.readAsDataURL(file);
        });
    });



    // ------------------------------------------
    // ADD — Submit
    // ------------------------------------------
    $('#addStudentForm').submit(function (e) {
        e.preventDefault();

        $('.btnSubmit').prop('disabled', true).html(`<i class="fas fa-spinner fa-spin"></i> Saving...`);

        let formData = new FormData(this);

        $.ajax({
            url: "/admin/students/store",
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,

            success: function () {
                Swal.fire("Success", "Student added successfully!", "success");

                $('#studentModal').modal('hide');
                $('#addStudentForm')[0].reset();
                $("#previewContainer").html("");

                table.ajax.reload(null, false);
                $('.btnSubmit').prop('disabled', false).html(`<i class="fas fa-save"></i> Save`);
            },

            error: function (xhr) {
                $('.btnSubmit').prop('disabled', false).html(`<i class="fas fa-save"></i> Save`);

                let html = `<div class="alert alert-danger">`;
                $.each(xhr.responseJSON.errors, (k,v)=> html+= `<div>${v}</div>`);
                html += `</div>`;

                $('#addFormErrors').html(html);
            }
        });
    });



    // ------------------------------------------
    // VIEW STUDENT
    // ------------------------------------------
    $(document).on("click", ".viewBtn", function () {

        let id = $(this).data("id");

        $.get(`/admin/students/show/${id}`, function (res) {

            $("#viewName").text(res.name);
            $("#viewRoll").text(res.roll_no);

            $("#viewImages").html("");

            res.images.forEach(img => {
                $("#viewImages").append(`
                    <div class="image-box">
                        <img src="/storage/${img.image_path}">
                        <div class="small text-muted">${img.rekognition_face_id}</div>
                    </div>
                `);
            });

            $("#viewStudentModal").modal("show");
        });
    });



    // ------------------------------------------
    // EDIT — OPEN MODAL
    // ------------------------------------------
    $(document).on("click", ".editBtn", function () {

        let id = $(this).data("id");

        $.get(`/admin/students/show/${id}`, function (res) {

            $("#edit_id").val(res.id);
            $("#edit_name").val(res.name);
            $("#edit_roll").val(res.roll_no);

            $("#editImages").html("");

            res.images.forEach(img => {
                $("#editImages").append(`
                    <div class="image-box" id="img-${img.id}">
                        <img src="/storage/${img.image_path}">
                        
                        <i class="fas fa-trash delete-img-btn" onclick="deleteImage(${img.id})"></i>
                        
                        <div class="text-muted small">${img.rekognition_face_id}</div>
                    </div>
                `);
            });

            $("#editStudentModal").modal("show");
        });
    });



    // ------------------------------------------
    // EDIT — SUBMIT
    // ------------------------------------------
    $("#editStudentForm").submit(function (e) {
        e.preventDefault();

        $('.updateBtn').prop('disabled', true).html(`<i class="fas fa-spinner fa-spin"></i> Updating...`);

        let id = $("#edit_id").val();
        let formData = new FormData(this);

        $.ajax({
            url: `/admin/students/update/${id}`,
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,

            success: function () {
                Swal.fire("Updated!", "Student updated successfully!", "success");

                $("#editStudentModal").modal("hide");
                table.ajax.reload(null, false);

                $('.updateBtn').prop('disabled', false).html(`<i class="fas fa-save"></i> Update`);
            },

            error: function (xhr) {

                $('.updateBtn').prop('disabled', false).html(`<i class="fas fa-save"></i> Update`);

                let html = `<div class="alert alert-danger">`;
                $.each(xhr.responseJSON.errors, (k,v)=> html+= `<div>${v}</div>`);
                html += `</div>`;

                $("#editFormErrors").html(html);
            }
        });
    });



    // ------------------------------------------
    // DELETE STUDENT
    // ------------------------------------------
    $(document).on("click", ".deleteBtn", function () {

        let id = $(this).data("id");

        Swal.fire({
            title: "Are you sure?",
            text: "This student will be permanently deleted!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Delete"
        }).then((res) => {

            if (res.isConfirmed) {

                $.ajax({
                    url: `/admin/students/delete/${id}`,
                    method: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },

                    success: function () {
                        Swal.fire("Deleted!", "", "success");
                        table.ajax.reload(null, false);
                    }
                });
            }

        });

    });

});



// ------------------------------------------
// DELETE INDIVIDUAL IMAGE (B2 Mode)
// ------------------------------------------
function deleteImage(id) {

    Swal.fire({
        title: "Delete Image?",
        text: "This image will be removed from Rekognition also.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Delete"
    }).then((res) => {

        if (!res.isConfirmed) return;

        $.ajax({
            url: "/admin/students/image/delete",
            method: "DELETE",
            data: {
                id: id,
                _token: "{{ csrf_token() }}"
            },
            success: function () {

                $("#img-" + id).fadeOut(200, function(){ $(this).remove(); });

                Swal.fire("Deleted!", "Image removed.", "success");
            }
        });

    });
}

</script>

@stop
