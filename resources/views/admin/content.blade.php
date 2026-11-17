<x-admin-layout :title="$definition['label']">
<div class="mb-8 flex flex-wrap items-start justify-between gap-4"><div><a class="text-sm text-blue-700" href="{{ route('admin.dashboard') }}">Back to all website content</a><h1 class="page-title mt-4">{{ $definition['label'] }}</h1><p class="page-intro">1. Choose a section. 2. Edit text or photographs. 3. Save and preview, then publish.</p></div><a class="btn-secondary" href="{{ route($pageRoute) }}" target="_blank" rel="noopener">View live page</a></div>
<form method="POST" action="{{ route('admin.content.update', $slug) }}" data-editor data-initial-dirty="{{ session()->hasOldInput() ? 'true' : 'false' }}">@csrf @method('PUT')<input type="hidden" name="version" value="{{ $record?->version ?? 0 }}">
<div class="grid items-start gap-6 xl:grid-cols-[minmax(0,1fr)_300px]">
<div class="min-w-0">
<div class="panel mb-6"><label class="mb-3 block text-sm font-semibold" for="find-field">Find something on this page</label><input id="find-field" class="input" data-filter placeholder="Search headings, text or photograph names"><p class="mt-3 text-xs text-slate-500" data-search-summary aria-live="polite"></p><nav class="mt-4 flex flex-wrap gap-2" aria-label="Editor sections">@foreach($groups as $group => $groupFields)<a class="rounded-none bg-blue-50 px-3 py-2 text-xs text-blue-700" href="#editor-group-{{ $loop->iteration }}">{{ $group }}</a>@endforeach</nav></div>
@foreach($groups as $group => $groupFields)
<details id="editor-group-{{ $loop->iteration }}" class="panel editor-group mb-5" data-field-group open><summary class="cursor-pointer text-base font-semibold text-slate-900">{{ $group }} <span class="ml-2 text-xs font-normal text-slate-500">{{ count($groupFields) }} fields</span></summary><div class="mt-6 grid gap-6">
@foreach($groupFields as $key => $field)
<div class="field border-t border-slate-100 pt-5" data-searchable><label for="field-{{ $key }}">{{ $field['label'] }}</label>
@if(isset($field['help']))<p class="text-xs leading-6 text-slate-500">{{ $field['help'] }}</p>@endif
@if($field['type'] === 'textarea')<textarea id="field-{{ $key }}" class="input" name="fields[{{ $key }}]" rows="4" maxlength="15000">{{ old('fields.'.$key, $values[$key]) }}</textarea>
@elseif($field['type'] === 'asset')
<x-admin-image-picker :id="'field-'.$key" :name="'fields['.$key.']'" :value="old('fields.'.$key, $values[$key])" :options="$assetOptions" :required="$field['required'] ?? false"/>
@elseif($field['type'] === 'select')<select class="input" id="field-{{ $key }}" name="fields[{{ $key }}]">@foreach($field['options'] as $option => $label)<option value="{{ $option }}" @selected(old('fields.'.$key, $values[$key]) === $option)>{{ $label }}</option>@endforeach</select>
@elseif(in_array($field['type'], ['image', 'pdf']))
@php
    $options = collect($files)->filter(fn ($file) => $field['type'] === 'pdf' ? str_ends_with($file, '.pdf') : !str_ends_with($file, '.pdf'))->map(fn ($file) => ['value' => $file, 'label' => basename($file), 'url' => route('media.show', basename($file))])->values()->all();
    $original = $field['type'] === 'pdf' ? asset('downloads/ideal-woman-organisational-profile.pdf') : (str_starts_with($key, 'partner-') ? (collect(glob(public_path('images/partners/'.substr($key, 8).'.*')))->map(fn ($path) => asset('images/partners/'.basename($path)))->first() ?? '') : asset('images/'.$key.($key === 'logo' ? '.png' : '.jpg')));
@endphp
<x-admin-image-picker :id="'field-'.$key" :name="'fields['.$key.']'" :value="old('fields.'.$key, $values[$key])" :options="$options" mode="path" empty-label="Use original website file" :original-url="$original" :pdf="$field['type'] === 'pdf'"/>
@else<input class="input" id="field-{{ $key }}" name="fields[{{ $key }}]" type="{{ in_array($field['type'], ['email', 'url']) ? $field['type'] : 'text' }}" value="{{ old('fields.'.$key, $values[$key]) }}" maxlength="15000">@endif
@error('fields.'.$key)<p class="text-sm text-red-700" role="alert">{{ $message }}</p>@enderror
</div>
@endforeach
</div></details>
@endforeach
</div>
<aside class="grid gap-5 xl:sticky xl:top-6"><div class="panel"><h2 class="font-semibold">Save and publish</h2><span class="badge mt-4">{{ $record && $record->draft !== $record->published ? 'Private draft changes' : ($record?->published_at ? 'Published' : 'Original content is live') }}</span>@if($record)<p class="mt-3 text-xs text-slate-500">Last saved {{ $record->updated_at->timezone('Africa/Kampala')->format('d M Y, H:i') }}</p>@endif<p class="mt-4 text-sm text-slate-600" data-save-status role="status">No unsaved changes</p><p class="mt-3 text-xs leading-6 text-slate-500">Save a draft to keep your work private. Preview saves your latest changes before opening the page. Publish updates the live website.</p><div class="mt-5 grid gap-3"><button class="btn-secondary" name="action" value="draft">Save draft</button>@if($slug === 'galleries')<label for="preview-page" class="text-sm font-semibold">Gallery to preview</label><select id="preview-page" name="preview_page" class="input"><option value="work">Climate activity on Our Work</option><option value="peace">Peace activity story</option></select>@endif<button class="btn-secondary" name="action" value="preview">Save and preview</button><button class="btn" name="action" value="publish">Publish changes</button></div></div>
<div class="panel"><h2 class="text-sm font-semibold">Other parts of this page</h2><p class="mt-2 text-xs leading-6 text-slate-500">Shared content updates every page where it appears.</p><nav class="mt-4 grid gap-3" aria-label="Related editors">@foreach($definitions as $relatedSlug => $relatedDefinition)@if($relatedSlug !== $slug && ($site->pageRoute($relatedSlug) === $pageRoute || ($slug === 'home' && str_starts_with($relatedSlug, 'programme-')) || in_array($relatedSlug, ['shared','settings','social','media','navigation','partnership','profile-download'])))<a class="text-sm text-blue-700 hover:underline" href="{{ route('admin.content.edit', $relatedSlug) }}">{{ $relatedDefinition['label'] }}</a>@endif @endforeach</nav></div></aside>
</div></form>
@include('admin.revisions', ['sectionSlug' => $slug, 'version' => $record?->version ?? 0, 'fieldDefinitions' => $definition['fields']])
</x-admin-layout>
