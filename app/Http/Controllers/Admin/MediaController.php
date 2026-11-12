<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MediaController extends Controller
{
    public function index(): View
    {
        return view('admin.media', ['files' => array_reverse(Storage::disk('local')->files('cms'))]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'extensions:jpg,jpeg,png,webp,pdf', 'max:10240']]);
        $file = $request->file('file');
        $name = Str::limit(Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)), 80, '') ?: 'upload';
        $path = $file->storeAs('cms', $name.'-'.Str::random(12).'.'.$file->extension(), 'local');

        return back()->with('success', 'File uploaded. Select it in Images & downloads or a story.')->with('uploaded', $path);
    }

    public function show(string $path): BinaryFileResponse
    {
        abort_unless(preg_match('/\A[a-zA-Z0-9-]+\.(jpg|jpeg|png|webp|pdf)\z/', $path) === 1, 404);
        $disk = Storage::disk('local');
        abort_unless($disk->exists('cms/'.$path), 404);

        return response()->file($disk->path('cms/'.$path), ['X-Content-Type-Options' => 'nosniff']);
    }
}
