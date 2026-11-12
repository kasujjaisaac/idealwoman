<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveContentRequest;
use App\Models\ContentRevision;
use App\Models\ContentSection;
use App\Services\SiteContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ContentController extends Controller
{
    public function edit(string $section, SiteContent $content): View
    {
        $definition = $content->definitions()[$section] ?? null;
        abort_if($definition === null, 404);
        $record = ContentSection::query()->where('slug', $section)->first();

        return view('admin.content', ['slug' => $section, 'definition' => $definition, 'record' => $record, 'values' => array_replace($content->defaults($section), $record?->draft ?? []), 'revisions' => $record?->revisions()->with('author')->latest('id')->limit(20)->get() ?? collect(), 'files' => Storage::disk('local')->files('cms'), 'images' => $content->imageNames()]);
    }

    public function update(SaveContentRequest $request, string $section, SiteContent $content): RedirectResponse
    {
        $data = $request->validated();
        $values = array_replace($content->defaults($section), array_map(fn (mixed $value): string => (string) $value, $data['fields']));
        foreach ($content->definitions()[$section]['fields'] as $key => $field) {
            if ($field['type'] === 'asset' && $values[$key] !== '' && ! in_array($values[$key], $content->imageNames(), true)) {
                throw ValidationException::withMessages(['fields.'.$key => 'Choose an image from the library.']);
            }
            if (in_array($field['type'], ['image', 'pdf']) && $values[$key] !== '') {
                $extension = pathinfo($values[$key], PATHINFO_EXTENSION);
                if (! Storage::disk('local')->exists($values[$key]) || ($field['type'] === 'pdf' ? $extension !== 'pdf' : ! in_array($extension, ['jpg', 'jpeg', 'png', 'webp']))) {
                    throw ValidationException::withMessages(['fields.'.$key => 'Choose a valid uploaded file of the correct type.']);
                }
            }
        }
        $content->save($section, $content->definitions()[$section]['label'], 'page', $values, $data['action'], $request->user(), (int) $data['version']);

        return back()->with('success', $data['action'] === 'publish' ? 'Changes published to the website.' : 'Draft saved. The live website has not changed.');
    }

    public function restore(Request $request, string $section, ContentRevision $revision, SiteContent $content): RedirectResponse
    {
        $record = ContentSection::query()->where('slug', $section)->firstOrFail();
        abort_unless($revision->content_section_id === $record->id, 404);
        $request->validate(['version' => ['required', 'integer', 'min:1']]);
        $content->save($section, $record->label, $record->kind, $revision->snapshot, 'restore', $request->user(), $request->integer('version'));

        return back()->with('success', 'Revision restored as a draft. Review it before publishing.');
    }
}
