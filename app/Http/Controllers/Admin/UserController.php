<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseCrudController;
use App\Models\User;
use Illuminate\Validation\Rule;

class UserController extends BaseCrudController
{
    protected $model = User::class;
    protected $view = 'admin.users';
    protected $folder = 'users';

    protected $fields = [
        "name" => [
            "label" => "Name",
            "type" => "text"
        ],
        "email" => [
            "label" => "Email",
            "type" => "email"
        ],
        "avatar" => [
            "label" => "Avatar",
            "type" => "file"
        ]
    ];

    protected $imageFields = [
        "avatar" => ["multiple" => false]
    ];

    /** Validation rules */
    protected $rules = [
        "name" => "required|string",
        "email" => [
            "required",
            "email",
            Rule::unique("users", "email")->ignore(request()->id)
        ],
        "avatar" => "nullable|image|max:4096"
    ];
}
