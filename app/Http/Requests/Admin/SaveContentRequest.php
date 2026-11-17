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
        $rules = ['version' => ['required', 'integer', 'min:0'], 'action' => ['required', Rule::in(['draft', 'publish', 'preview'])], 'fields' => ['required', 'array:'.implode(',', array_keys($fields))]];
        if ($this->route('section') === 'galleries') {
            $rules['preview_page'] = ['sometimes', Rule::in(['work', 'peace'])];
        }
        foreach ($fields as $key => $field) {
            if (in_array($field['type'], ['image', 'pdf'])) {
                $rules['fields.'.$key] = ['present', 'nullable', 'string', 'regex:/\Acms\/[a-zA-Z0-9-]+\.(jpg|jpeg|png|webp|pdf)\z/'];
            } else {
                $rules['fields.'.$key] = ['present', ($field['required'] ?? false) ? 'required' : 'nullable', 'string', $key === 'title' ? 'max:200' : 'max:15000'];
                if ($field['type'] === 'select') {
                    $rules['fields.'.$key][] = Rule::in(array_keys($field['options']));
                }
                if ($field['type'] === 'url') {
                    $rules['fields.'.$key][] = 'url:https';
                } elseif ($field['type'] === 'email') {
                    $rules['fields.'.$key][] = 'email';
                }
            }
        }
        if ($this->route('section') === 'galleries') {
            foreach ($fields as $key => $field) {
                if (str_ends_with($key, '_alt')) {
                    $rules['fields.'.$key][] = Rule::requiredIf($this->filled('fields.'.substr($key, 0, -4).'_image'));
                }
            }
        }

        return $rules;
    }

    public function attributes(): array
    {
        $definition = app(SiteContent::class)->definitions()[$this->route('section')] ?? ['fields' => []];
        $attributes = [];
        foreach ($definition['fields'] as $key => $field) {
            $attributes['fields.'.$key] = $field['label'];
        }

        return $attributes;
    }
}
