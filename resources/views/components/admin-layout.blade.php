@props(['title' => 'Overview'])
<!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex, nofollow"><title>{{ $title }} ? Ideal Woman Admin</title><link rel="icon" href="{{ asset('images/logo.png') }}">@vite(['resources/css/admin.css', 'resources/js/admin.js'])</head>
<body class="bg-slate-50 text-slate-800 antialiased">
<a class="sr-only focus:not-sr-only" href="#admin-main">Skip to content</a>
<div class="min-h-screen lg:grid lg:grid-cols-[250px_1fr]">
<aside class="border-r border-slate-200 bg-white p-6 lg:sticky lg:top-0 lg:h-screen lg:overflow-y-auto">
<a href="{{ route('admin.dashboard') }}" class="block"><img src="{{ asset('images/logo.png') }}" alt="Ideal Woman" class="w-44"><span class="mt-4 block text-[10px] font-bold uppercase tracking-[.24em] text-slate-400">Content studio</span></a>
<nav class="mt-7 grid gap-1" aria-label="Admin navigation">
@foreach(['admin.dashboard' => ['Overview', '?'], 'admin.stories.index' => ['Stories & updates', '?'], 'admin.media.index' => ['Media library', '?'], 'admin.enquiries.index' => ['Enquiries', '?']] as $route => [$label, $symbol])
<a href="{{ route($route) }}" @class(['nav-link', 'active' => request()->routeIs($route) || ($route === 'admin.stories.index' && request()->routeIs('admin.stories.*'))])><span aria-hidden="true">{{ $symbol }}</span>{{ $label }}</a>
@endforeach
<p class="nav-label">Website</p>
@foreach(['home'=>'Home','about'=>'About us','work'=>'Our work','stories'=>'Stories page','involved'=>'Get involved','contact'=>'Contact','shared'=>'Header & footer','settings'=>'Contact & SEO','social'=>'Social media','media'=>'Images & downloads','leadership'=>'Leadership','collaboration'=>'Partnerships','navigation'=>'Navigation labels'] as $key => $label)
<a href="{{ route('admin.content.edit', $key) }}" @class(['nav-link', 'active' => request()->route('section') === $key])>{{ $label }}</a>
@endforeach
@can('manage-users')<p class="nav-label">Administration</p><a class="nav-link" href="{{ route('admin.users.index') }}">Team & access</a>@endcan
<a class="nav-link" href="{{ route('admin.account') }}">My account</a>
</nav>
<div class="mt-8 border-t border-slate-100 pt-5 text-xs text-slate-500"><p class="font-semibold text-slate-800">{{ auth()->user()->name }}</p><p class="mt-1 capitalize">{{ auth()->user()->role }}</p><form method="POST" action="{{ route('admin.logout') }}" class="mt-3">@csrf<button class="text-blue-700 hover:underline">Sign out</button></form></div>
</aside>
<div class="min-w-0"><header class="flex items-center justify-between gap-4 border-b border-slate-200 bg-white px-6 py-5 lg:px-10"><p class="text-sm text-slate-500">Ideal Woman <span class="mx-2 text-slate-300">/</span><span class="text-slate-800">{{ $title }}</span></p><a href="{{ route('home') }}" target="_blank" rel="noopener" class="text-sm font-semibold text-blue-700">View website ?</a></header>
<main id="admin-main" class="mx-auto max-w-7xl p-6 lg:p-10">
@if(session('success'))<div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800" role="status">{{ session('success') }}</div>@endif
@if($errors->any())<div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert"><p class="font-semibold">Please check your changes.</p><ul class="mt-2 list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
{{ $slot }}
</main><footer class="px-6 pb-8 text-xs text-slate-400 lg:px-10">Ideal Woman ? Empowering women. Strengthening communities.</footer></div></div>
</body></html>
