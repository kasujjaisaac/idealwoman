@props(['name', 'id', 'value' => '', 'options' => [], 'mode' => 'asset', 'emptyLabel' => 'No image', 'originalUrl' => '', 'pdf' => false, 'required' => false])
@php($selectedUrl = collect($options)->firstWhere('value', $value)['url'] ?? $originalUrl)
<div data-image-picker data-mode="{{ $mode }}" data-pdf="{{ $pdf ? 'true' : 'false' }}" data-upload-url="{{ route('admin.media.store') }}" data-original-url="{{ $originalUrl }}" class="grid gap-3">
    @unless($pdf)<img data-image-preview @if($selectedUrl) src="{{ $selectedUrl }}" @endif alt="Selected photograph preview" class="h-44 w-full rounded-xl bg-slate-100 object-contain" @if(!$selectedUrl) hidden @endif>@endunless
    <select class="input" id="{{ $id }}" name="{{ $name }}" data-image-select @required($required)>
        <option value="">{{ $emptyLabel }}</option>
        @foreach($options as $option)<option value="{{ $option['value'] }}" data-image-url="{{ $option['url'] }}" @selected($value === $option['value'])>{{ $option['label'] }}</option>@endforeach
    </select>
    <div class="flex flex-wrap items-center gap-3">
        @unless($pdf)<button type="button" class="btn-secondary" data-open-library hidden>Browse photographs</button>@endunless
        <label class="btn-secondary cursor-pointer" for="upload-{{ $id }}">Upload {{ $pdf ? 'PDF' : 'photograph' }}</label>
        <input type="file" id="upload-{{ $id }}" data-inline-upload accept="{{ $pdf ? '.pdf' : '.jpg,.jpeg,.png,.webp' }}" class="w-full text-xs text-slate-500">
    </div>
    <p class="text-xs leading-6 text-slate-500">Uploads stay in the library. Save or publish this page to use your selection. Maximum 10 MB; photographs up to 12 megapixels.</p>
    <noscript><p class="text-sm text-slate-600">To upload without JavaScript, use the <a class="underline" target="_blank" rel="noopener" href="{{ route('admin.media.index') }}">media library</a>, save your draft, and reload this editor.</p></noscript><p data-upload-status class="text-sm text-blue-700" role="status" aria-live="polite"></p>
</div>
