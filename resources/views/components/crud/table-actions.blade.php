<div class="btn-group">

    <button class="btn btn-info btn-sm viewBtn" data-id="{{ $row->id }}">
        <i class="fas fa-eye"></i>
    </button>

    <button class="btn btn-warning btn-sm editBtn" data-id="{{ $row->id }}">
        <i class="fas fa-edit"></i>
    </button>

    <button class="btn btn-danger btn-sm deleteBtn" data-id="{{ $row->id }}">
        <i class="fas fa-trash-alt"></i>
    </button>

</div>
