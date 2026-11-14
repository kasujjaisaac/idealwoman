<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ImageOptimizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MediaController extends Controller
{
    public function index(): View
    {
        return view('admin.media', ['files' => array_reverse(Storage::disk('local')->files('cms'))]);
    }

    public function store(Request $request, ImageOptimizer $optimizer): RedirectResponse
    {
        $request->validate(['file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'extensions:jpg,jpeg,png,webp,pdf', 'max:10240']]);
        $file = $request->file('file');
        $isImage = $file->getMimeType() !== 'application/pdf';
        if ($isImage) {
            $dimensions = @getimagesize($file->getRealPath());
            if (! $dimensions || $dimensions[0] * $dimensions[1] > 12000000 || max($dimensions[0], $dimensions[1]) > 10000) {
                throw ValidationException::withMessages(['file' => 'Choose a valid image with no more than 12 megapixels and 10,000 pixels on either side.']);
            }
        }
        $name = Str::limit(Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)), 80, '') ?: 'upload';
        $path = $file->storeAs('cms', $name.'-'.Str::random(12).'.'.$file->extension(), 'local');
        if ($isImage) {
            $disk = Storage::disk('local');
            $basename = pathinfo($path, PATHINFO_FILENAME);
            try {
                $metadata = $optimizer->generate($disk->path($path), $disk->path('cms/variants'), $basename);
                $disk->put('cms/variants/'.$basename.'.json', json_encode($metadata, JSON_THROW_ON_ERROR));
            } catch (\Throwable $exception) {
                $disk->delete($path);
                report($exception);
                throw ValidationException::withMessages(['file' => 'This image could not be processed. Try another image or contact the website administrator.']);
            }
        }

        return back()->with('success', 'File uploaded. Select it in Images & downloads or a story.')->with('uploaded', $path);
    }

    public function show(string $path): BinaryFileResponse
    {
        abort_unless(preg_match('/\A[a-zA-Z0-9-]+\.(jpg|jpeg|png|webp|pdf)\z/', $path) === 1, 404);
        $disk = Storage::disk('local');
        $storedPath = $disk->exists('cms/'.$path) ? 'cms/'.$path : 'cms/variants/'.$path;
        abort_unless($disk->exists($storedPath), 404);

        return response()->file($disk->path($storedPath), [
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'public, max-age=2592000',
        ]);
    }
}
