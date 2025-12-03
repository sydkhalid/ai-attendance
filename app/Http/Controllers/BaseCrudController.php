<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

abstract class BaseCrudController extends Controller
{
    protected $model;        // Example: Student::class
    protected $view;         // Example: admin.students
    protected $folder;       // storage/app/public/{folder}
    protected $rules = [];   // validation rules
    protected $imageFields = [
        // 'face_image' => ['multiple' => false],
        // 'gallery'    => ['multiple' => true]
    ];

    /* --------------------------------------------
     * INDEX PAGE
     * -------------------------------------------- */
    public function index()
    {
        return view($this->view . '.index', [
            'imageFields' => $this->imageFields
        ]);
    }

    /* --------------------------------------------
     * LIST FOR DATATABLE
     * -------------------------------------------- */
    public function list(Request $r)
    {
        $model = $this->model;
        return $model::query()->orderBy('id', 'DESC')->paginate(50);
    }

    /* --------------------------------------------
     * STORE RECORD
     * -------------------------------------------- */
    public function store(Request $request)
    {
        $validated = $request->validate($this->rules);

        $model = new $this->model;
        $model->fill($validated);
        $model->save();

        /* ---- Handle Single & Multiple Image Fields ---- */
        $this->saveImages($model, $request);

        return response()->json(['success' => true]);
    }

    /* --------------------------------------------
     * SHOW ONE
     * -------------------------------------------- */
    public function show($id)
    {
        $model = $this->model;
        return $model::findOrFail($id);
    }

    /* --------------------------------------------
     * UPDATE
     * -------------------------------------------- */
    public function update(Request $request, $id)
    {
        $model = $this->model;
        $record = $model::findOrFail($id);

        $validated = $request->validate($this->rules);

        $record->fill($validated);
        $record->save();

        $this->saveImages($record, $request);

        return response()->json(['success' => true]);
    }

    /* --------------------------------------------
     * DELETE ONE
     * -------------------------------------------- */
    public function destroy($id)
    {
        $model = $this->model;
        $record = $model::findOrFail($id);

        // delete single images
        foreach ($this->imageFields as $field => $opt) {
            if (empty($opt['multiple']) && !empty($record->$field)) {
                Storage::delete("public/{$record->$field}");
            }
        }

        $record->delete();
        return response()->json(['success' => true]);
    }

    /* --------------------------------------------
     * SAVE IMAGES (single + multiple)
     * -------------------------------------------- */
    protected function saveImages($record, Request $request)
    {
        foreach ($this->imageFields as $field => $opt) {

            $isMultiple = $opt['multiple'] ?? false;

            /* ------------ SINGLE IMAGE ------------ */
            if (!$isMultiple) {
                if ($request->hasFile($field)) {

                    // delete old
                    if ($record->$field) {
                        Storage::delete("public/{$record->$field}");
                    }

                    $path = $request->file($field)->store("{$this->folder}/{$record->id}", "public");
                    $record->$field = $path;
                    $record->save();
                }
            }

            /* ------------ MULTIPLE IMAGES ------------ */
            else {
                if ($request->hasFile($field)) {

                    foreach ($request->file($field) as $file) {
                        $path = $file->store("{$this->folder}/{$record->id}/gallery", "public");

                        // save in child table
                        $record->gallery()->create([
                            'image_path' => $path
                        ]);
                    }
                }
            }
        }
    }
}
