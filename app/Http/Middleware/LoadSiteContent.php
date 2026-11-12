<?php

namespace App\Http\Middleware;

use App\Services\SiteContent;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class LoadSiteContent
{
    public function handle(Request $request, Closure $next): Response
    {
        $content = new SiteContent;
        $content->load();
        if ($request->has('preview')) {
            Gate::authorize('manage-content');
            $request->validate(['preview' => ['required', 'string', 'max:200']]);
            $content->preview((string) $request->query('preview'));
        }
        View::share('site', $content);
        $original = config('idealwoman');
        config(['idealwoman' => $content->publicConfiguration()]);
        try {
            $response = $next($request);
            if (($request->is('admin', 'admin/*') || $request->has('preview'))) {
                $response->headers->set('Cache-Control', 'no-store, private');
                $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
            }

            return $response;
        } finally {
            config(['idealwoman' => $original]);
        }
    }
}
