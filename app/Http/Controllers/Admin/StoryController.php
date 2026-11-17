<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveStoryRequest;
use App\Models\ContentSection;
use App\Services\SiteContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StoryController extends Controller
{
    public function index(): View
    {
        return view('admin.stories', ['stories' => ContentSection::query()->where('kind', 'story')->latest('id')->paginate(15)]);
    }

    public function create(SiteContent $content): View
    {
        return view('admin.story', ['record' => null, 'values' => [], 'assetOptions' => $content->imageOptions(), 'revisions' => collect()]);
    }

    public function edit(ContentSection $story, SiteContent $content): View
    {
        abort_unless($story->kind === 'story', 404);

        return view('admin.story', ['record' => $story, 'values' => $story->draft, 'assetOptions' => $content->imageOptions(), 'revisions' => $story->revisions()->with('author')->latest('id')->limit(20)->get()]);
    }

    public function store(SaveStoryRequest $request, SiteContent $content): RedirectResponse
    {
        $slug = 'story-'.$request->validated('slug');
        if (ContentSection::query()->where('slug', $slug)->exists()) {
            throw ValidationException::withMessages(['slug' => 'This story URL is already in use.']);
        }

        return $this->persist($request, $content, $slug);
    }

    public function update(SaveStoryRequest $request, ContentSection $story, SiteContent $content): RedirectResponse
    {
        abort_unless($story->kind === 'story', 404);
        if ('story-'.$request->validated('slug') !== $story->slug) {
            throw ValidationException::withMessages(['slug' => 'The URL is fixed after creation to protect existing links.']);
        }

        return $this->persist($request, $content, $story->slug);
    }

    public function unpublish(Request $request, ContentSection $story, SiteContent $content): RedirectResponse
    {
        abort_unless($story->kind === 'story', 404);
        $request->validate(['version' => ['required', 'integer', 'min:1']]);
        $content->save($story->slug, $story->label, 'story', $story->draft, 'unpublish', $request->user(), $request->integer('version'));

        return back()->with('success', 'Story unpublished. Its content and revisions are retained.');
    }

    private function persist(SaveStoryRequest $request, SiteContent $content, string $slug): RedirectResponse
    {
        $data = $request->safe()->only(['title', 'category', 'excerpt', 'body', 'image', 'alt']);
        if (! in_array($data['image'], $this->images(), true)) {
            throw ValidationException::withMessages(['image' => 'Choose an image from the library.']);
        }
        $record = $content->save($slug, $data['title'], 'story', $data, $request->validated('action') === 'preview' ? 'draft' : $request->validated('action'), $request->user(), $request->integer('version'));
        if ($request->validated('action') === 'preview') {
            return to_route('stories.show', ['slug' => substr($slug, 6), 'preview' => $slug]);
        }

        return to_route('admin.stories.edit', $record)->with('success', $request->validated('action') === 'publish' ? 'Story published.' : 'Story draft saved.');
    }

    /** @return array<int,string> */
    private function images(): array
    {
        $images = array_map(fn (string $path): string => pathinfo($path, PATHINFO_FILENAME), glob(public_path('images/*.jpg')) ?: []);
        foreach (Storage::disk('local')->files('cms') as $file) {
            if (in_array(pathinfo($file, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'webp'], true)) {
                $images[] = pathinfo($file, PATHINFO_FILENAME);
            }
        }

        return $images;
    }
}
