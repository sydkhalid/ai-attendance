$(document).ready(function () {

    const crudRoutes   = window.crudConfig.routes;
    const fields       = window.crudConfig.fields;
    const imageFields  = window.crudConfig.imageFields;
    window.csrf_token  = $('meta[name="csrf-token"]').attr("content");

    /* -------------------------------------------------------
     * DATATABLE
     * ------------------------------------------------------- */
    const tableCols = [{ data: "id", name: "id" }];

    Object.keys(fields).forEach(name => {
        tableCols.push({
            data: name,
            name: name,
            render: function (value) {

                // SINGLE IMAGE
                if (fields[name].type === "file" && !(imageFields[name]?.multiple)) {
                    if (!value) return "-";
                    return `<img src="/storage/${value}" width="50" class="img-thumbnail">`;
                }

                // MULTIPLE IMAGES (BADGE ONLY)
                if (fields[name].type === "file" && imageFields[name]?.multiple) {
                    return `<span class="badge badge-info">Gallery</span>`;
                }

                return value ?? "-";
            }
        });
    });

    tableCols.push({
        data: "actions",
        name: "actions",
        orderable: false,
        searchable: false
    });

    const crudTable = $("#crudTable").DataTable({
        processing: true,
        serverSide: true,
        ajax: crudRoutes.list,
        columns: tableCols
    });

    /* -------------------------------------------------------
     * ADD RECORD
     * ------------------------------------------------------- */
    $(".crudAddBtn").click(function (e) {
        e.preventDefault();

        let formData = new FormData($("#crudAddForm")[0]);

        $.ajax({
            url: crudRoutes.store,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function () {
                $("#crudAddModal").modal("hide");
                $("#crudAddForm")[0].reset();
                $(".preview-container").empty();
                crudTable.ajax.reload();
                Swal.fire("Success", "Record added successfully!", "success");
            },
            error: function (xhr) {
                showErrors("#crudAddErrors", xhr);
            }
        });
    });

    /* -------------------------------------------------------
     * VIEW RECORD
     * ------------------------------------------------------- */
    $(document).on("click", ".viewBtn", function () {
        let id = $(this).data("id");

        $.get(crudRoutes.show + "/" + id, function (data) {

            let html = "";

            Object.keys(fields).forEach(name => {
                html += `<p><b>${fields[name].label}:</b> `;

                if (fields[name].type === "file" && !imageFields[name]?.multiple) {
                    html += data[name]
                        ? `<br><img src="/storage/${data[name]}" width="120" class="img-thumbnail">`
                        : "-";
                } else {
                    html += data[name] ?? "-";
                }

                html += `</p>`;
            });

            // Load gallery
            $.get(`/students/gallery/${id}`, function (gallery) {
                if (gallery.length > 0) {
                    html += `<h5>Gallery</h5>`;
                    gallery.forEach(img => {
                        html += `<img src="/storage/${img.image_path}" 
                                    width="120" class="img-thumbnail mr-2 mb-2">`;
                    });
                }
            });

            $("#crudViewBody").html(html);
            $("#crudViewModal").modal("show");
        });
    });

    /* -------------------------------------------------------
     * EDIT LOAD
     * ------------------------------------------------------- */
    $(document).on("click", ".editBtn", function () {

        let id = $(this).data("id");

        $("#crudEditForm")[0].reset();
        $("#crudEditErrors").html("");
        $("#crudEditImages").empty();
        $(".preview-container").empty();

        $.get(crudRoutes.show + "/" + id, function (data) {

            $("#crudEditForm input[name=id]").val(id);

            // Fill all simple fields
            Object.keys(fields).forEach(name => {
                if (fields[name].type !== "file") {
                    $("#crudEditForm [name=" + name + "]").val(data[name]);
                }
            });

            // Face image preview
            if (data.face_image) {
                $("#crudEditImages").append(`
                    <div class="mb-3">
                        <b>Face Image</b><br>
                        <img src="/storage/${data.face_image}" 
                             width="120" class="img-thumbnail">
                    </div>
                `);
            }

            // Load gallery
            $.get(`/students/gallery/${id}`, function (gallery) {

                if (gallery.length > 0) {
                    $("#crudEditImages").append(`<h5>Gallery Images</h5>`);
                }

                gallery.forEach(img => {

                    $("#crudEditImages").append(`
                        <div class="thumb-box old" 
                             data-img-id="${img.id}"
                             style="position:relative; display:inline-block; margin:8px;">

                            <span class="delete-old">&times;</span>

                            <img src="/storage/${img.image_path}"
                                 width="100" height="100" 
                                 class="img-thumbnail">
                        </div>
                    `);
                });
            });

            $("#crudEditModal").modal("show");
        });
    });

    /* -------------------------------------------------------
     * DELETE OLD GALLERY IMAGE
     * ------------------------------------------------------- */
    $(document).on("click", ".delete-old", function () {

        let box = $(this).closest(".thumb-box");
        let imageId = box.data("img-id");

        Swal.fire({
            title: "Delete this image?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Delete"
        }).then(res => {

            if (!res.isConfirmed) return;

            $.ajax({
                url: "/students/gallery-delete/" + imageId,
                method: "DELETE",
                data: { _token: csrf_token },
                success: function () {
                    box.remove();
                }
            });
        });
    });

    /* -------------------------------------------------------
     * UPDATE RECORD
     * ------------------------------------------------------- */
    $(".crudUpdateBtn").click(function (e) {
        e.preventDefault();

        let id = $("#crudEditForm input[name=id]").val();
        let formData = new FormData($("#crudEditForm")[0]);

        $.ajax({
            url: crudRoutes.update + "/" + id,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function () {
                $("#crudEditModal").modal("hide");
                crudTable.ajax.reload();
                Swal.fire("Updated!", "Record updated successfully!", "success");
            },
            error: function (xhr) {
                showErrors("#crudEditErrors", xhr);
            }
        });
    });

    /* -------------------------------------------------------
     * PREVIEW NEW FILES
     * ------------------------------------------------------- */
    $(document).on("change", "input[type='file']", function () {

        let field = $(this).attr("name").replace("[]", "");
        let container = $("#preview-" + field);

        container.html("");

        [...this.files].forEach((file, index) => {
            let reader = new FileReader();

            reader.onload = function (e) {
                container.append(`
                    <div class="thumb-box new" data-index="${index}">
                        <span class="thumb-remove">&times;</span>
                        <img src="${e.target.result}" width="100" height="100">
                    </div>
                `);
            };

            reader.readAsDataURL(file);
        });
    });

    /* -------------------------------------------------------
     * REMOVE NEW PREVIEW IMAGE
     * ------------------------------------------------------- */
    $(document).on("click", ".thumb-remove", function () {
        $(this).closest(".thumb-box").remove();
    });

    /* -------------------------------------------------------
     * SHOW ERROR MESSAGES
     * ------------------------------------------------------- */
    function showErrors(target, xhr) {
        let html = `<div class="alert alert-danger">`;

        $.each(xhr.responseJSON.errors, function (key, val) {
            html += `<p>${val[0]}</p>`;
        });

        html += `</div>`;
        $(target).html(html);
    }

});
