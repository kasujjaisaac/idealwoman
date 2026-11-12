<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveStoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-content') ?? false;
    }

    public function rules(): array
    {
        return ['slug' => ['required', 'string', 'max:150', 'regex:/\A[a-z0-9]+(?:-[a-z0-9]+)*\z/'], 'title' => ['required', 'string', 'max:200'], 'category' => ['required', 'string', 'max:120'], 'excerpt' => ['required', 'string', 'max:1000'], 'body' => ['required', 'string', 'max:50000'], 'image' => ['required', 'string', 'max:150', 'regex:/\A[a-zA-Z0-9-]+\z/'], 'alt' => ['required', 'string', 'max:500'], 'action' => ['required', Rule::in(['draft', 'publish'])], 'version' => ['required', 'integer', 'min:0']];
    }
}
