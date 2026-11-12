<?php

namespace App\Http\Requests\Admin;

use App\Services\SiteContent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-content') ?? false;
    }

    public function rules(SiteContent $content): array
    {
        $definition = $content->definitions()[$this->route('section')] ?? null;
        abort_if($definition === null, 404);
        $fields = $definition['fields'];
        $rules = ['version' => ['required', 'integer', 'min:0'], 'action' => ['required', Rule::in(['draft', 'publish'])], 'fields' => ['required', 'array:'.implode(',', array_keys($fields))]];
        foreach ($fields as $key => $field) {
            if (in_array($field['type'], ['image', 'pdf'])) {
                $rules['fields.'.$key] = ['present', 'nullable', 'string', 'regex:/\Acms\/[a-zA-Z0-9-]+\.(jpg|jpeg|png|webp|pdf)\z/'];
            } else {
                $rules['fields.'.$key] = ['present', 'nullable', 'string', $key === 'title' ? 'max:200' : 'max:15000'];
                if ($field['type'] === 'url') {
                    $rules['fields.'.$key][] = 'url:https';
                } elseif ($field['type'] === 'email') {
                    $rules['fields.'.$key][] = 'email';
                }
            }
        }

        return $rules;
    }
}
