@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="mb-1">{{ $config['title'] }}</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
            <li class="breadcrumb-item active">{{ $config['title'] }}</li>
        </ol>
    </div>

    <button class="btn btn-primary" data-toggle="modal" data-target="#crudAddModal">
        <i class="fas fa-plus-circle"></i> Add {{ $config['title'] }}
    </button>
</div>
@stop

<div class="card">
    <div class="card-body">

        <table id="crudTable" class="table table-bordered w-100">
            <thead>
                <tr>
                    <th>ID</th>

                    @foreach($config['fields'] as $n => $f)
                        <th>{{ $f['label'] }}</th>
                    @endforeach

                    <th>Actions</th>
                </tr>
            </thead>
        </table>

    </div>
</div>

{{-- AUTO-MODALS --}}
@include('components.crud.modal-add', $config)
@include('components.crud.modal-edit', $config)
@include('components.crud.modal-view', $config)
