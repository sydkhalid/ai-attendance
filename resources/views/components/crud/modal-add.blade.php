<div class="modal fade" id="crudAddModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form id="crudAddForm" enctype="multipart/form-data">

                <div class="modal-header">
                    <h5>Add {{ $title }}</h5>
                    <button class="close" data-dismiss="modal">×</button>
                </div>

                <div class="modal-body">
                    <div id="crudAddErrors"></div>

                    @foreach($fields as $name => $f)
                        @if(($f['type'] ?? '') !== 'file')
                            {{-- TEXT INPUTS --}}
                            <div class="form-group">
                                <label>{{ $f['label'] }}</label>
                                <input type="text" name="{{ $name }}" class="form-control">
                            </div>
                        @else
                            {{-- IMAGE INPUTS --}}
                            <div class="form-group">
                                <label>{{ $f['label'] }}</label>
                                <input type="file"
                                       name="{{ $imageFields[$name]['multiple'] ? $name.'[]' : $name }}"
                                       class="form-control-file"
                                       {{ $imageFields[$name]['multiple'] ? 'multiple' : '' }}>

                                <div id="preview-{{ $name }}" class="preview-container"></div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <div class="modal-footer">
                    <button class="btn btn-success crudAddBtn">Save</button>
                </div>

            </form>

        </div>
    </div>
</div>
