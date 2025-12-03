<div class="form-group mb-3">

    <label>{{ $label }}</label>

    <!-- Upload Zone -->
    <div class="upload-zone"
         data-field="{{ $name }}"
         style="border:2px dashed #ccc; padding:20px; cursor:pointer; text-align:center;">
        <p class="m-0">Click or Drag & Drop to Upload</p>
    </div>

    <!-- Hidden File Input -->
    <input type="file"
           id="file-{{ $name }}"
           name="{{ $multiple ? $name.'[]' : $name }}"
           class="d-none"
           {{ $multiple ? 'multiple' : '' }}>

    <!-- Preview Container -->
    <div id="preview-{{ $name }}" class="preview-container" style="margin-top:10px; display:flex; gap:10px; flex-wrap:wrap;">
        {{-- JS inserts previews here --}}
    </div>

</div>
