<div class="modal fade" id="crudEditModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form id="crudEditForm" enctype="multipart/form-data">
                <input type="hidden" name="id">

                <div class="modal-header">
                    <h5>Edit {{ $title }}</h5>
                    <button class="close" data-dismiss="modal">×</button>
                </div>

                <div class="modal-body">
                    <div id="crudEditErrors"></div>

                    {{-- OLD images preview --}}
                    <div id="crudEditImages" class="mb-3"></div>

                    @foreach($fields as $name => $f)

                        @if(($f['type'] ?? '') !== 'file')
                            {{-- TEXT INPUT --}}
                            <div class="form-group">
                                <label>{{ $f['label'] }}</label>
                                <input type="text" name="{{ $name }}" class="form-control">
                            </div>

                        @else
                            {{-- NEW FILE UPLOAD --}}
                            <div class="form-group">
                                <label>{{ $f['label'] }} (Upload new)</label>
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
                    <button class="btn btn-primary crudUpdateBtn">Update</button>
                </div>

            </form>

        </div>
    </div>
</div>
