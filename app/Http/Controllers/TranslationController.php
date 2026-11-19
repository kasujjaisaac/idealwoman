<?php

namespace App\Http\Controllers;

use App\Services\WebsiteTranslator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class TranslationController extends Controller
{
    public function __invoke(Request $request, WebsiteTranslator $translator): JsonResponse
    {
        $validated = $request->validate([
            'language' => ['required', Rule::in(['lg', 'sw', 'fr', 'ar', 'es', 'pt', 'zh-CN'])],
            'texts' => ['required', 'array', 'list', 'min:1', 'max:30'],
            'texts.*' => ['required', 'string', 'max:3000'],
        ]);
        if (array_sum(array_map(mb_strlen(...), $validated['texts'])) > 12000) {
            throw ValidationException::withMessages(['texts' => 'Please translate a smaller batch of text.']);
        }
        try {
            return response()->json(['translations' => $translator->translate($validated['texts'], $validated['language'])])
                ->header('Cache-Control', 'no-store');
        } catch (HttpExceptionInterface $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::warning('Website translation failed.', ['exception_type' => $exception::class]);

            return response()->json(['message' => 'Translation is temporarily unavailable. Please try again.'], 503);
        }
    }
}
