@props(['title' => 'Overview'])
@php
    $pageLabels = ['home' => 'Home page', 'about' => 'About us', 'work' => 'Our work', 'stories' => 'Stories page', 'involved' => 'Get involved', 'contact' => 'Contact page'];
    $currentSection = request()->route('section');
    $definitions = $site->definitions();
    $isShared = $currentSection && !isset($pageLabels[$currentSection]) && !str_starts_with($currentSection, 'programme-');
@endphp
<!DOCTYPE html><html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex, nofollow"><title>{{ $title }} | Ideal Woman Admin</title><link rel="icon" href="{{ $site->media('logo', asset('images/logo.png')) }}">@vite(['resources/css/admin.css', 'resources/js/admin.js'])</head>
<body class="admin-app font-sans antialiased">
<a class="admin-skip-link" href="#admin-main">Skip to content</a>
<div class="admin-shell">
<aside class="admin-sidebar">
<a href="{{ route('admin.dashboard') }}" class="studio-brand"><span class="studio-logo"><img src="{{ $site->media('logo', asset('images/logo.png')) }}" width="900" height="266" alt="Ideal Woman"></span><span class="studio-wordmark">Content studio <span>ADMIN</span></span></a>
<details data-admin-navigation open class="sidebar-disclosure"><summary class="mobile-menu-label">Menu <x-admin-icon name="chevron"/></summary>
<nav class="studio-navigation" aria-label="Admin navigation">
<p class="nav-label">Workspace</p>
@foreach(['admin.dashboard' => ['Overview', 'grid'], 'admin.stories.index' => ['Stories & updates', 'file'], 'admin.media.index' => ['Media library', 'image'], 'admin.enquiries.index' => ['Enquiries', 'mail']] as $route => [$label, $icon])
@php($active = request()->routeIs($route) || ($route === 'admin.stories.index' && request()->routeIs('admin.stories.*')))
<a href="{{ route($route) }}" @class(['nav-link', 'active' => $active]) @if($active) aria-current="page" @endif><x-admin-icon :name="$icon"/><span>{{ $label }}</span>@if($active)<span class="nav-active-mark" aria-hidden="true"></span>@endif</a>
@endforeach
<details class="nav-group" open><summary><span>Website pages</span><x-admin-icon name="chevron"/></summary><div class="nav-group-links">
@foreach($pageLabels as $key => $label)<a href="{{ route('admin.content.edit', $key) }}" @class(['nav-link', 'active' => $currentSection === $key]) @if($currentSection === $key) aria-current="page" @endif>{{ $label }}</a>@endforeach
</div></details>
<details class="nav-group" @if($isShared) open @endif><summary><span>Shared content & settings</span><x-admin-icon name="chevron"/></summary><div class="nav-group-links">
@foreach($definitions as $key => $section)@if(!isset($pageLabels[$key]) && !str_starts_with($key, 'programme-'))<a href="{{ route('admin.content.edit', $key) }}" @class(['nav-link', 'active' => $currentSection === $key]) @if($currentSection === $key) aria-current="page" @endif>{{ $section['label'] }}</a>@endif @endforeach
</div></details>
<details class="nav-group" @if(str_starts_with($currentSection ?? '', 'programme-')) open @endif><summary><span>Activities & programmes</span><x-admin-icon name="chevron"/></summary><div class="nav-group-links">
@foreach(config('idealwoman.work') as $key => $programme)<a href="{{ route('admin.content.edit', 'programme-'.$key) }}" @class(['nav-link', 'active' => $currentSection === 'programme-'.$key]) @if($currentSection === 'programme-'.$key) aria-current="page" @endif>{{ $programme['title'] }}</a>@endforeach
</div></details>
@can('manage-users')<p class="nav-label">Administration</p><a href="{{ route('admin.users.index') }}" @class(['nav-link', 'active' => request()->routeIs('admin.users.*')])><x-admin-icon name="shield"/><span>Team & access</span></a>@endcan
</nav>
<div class="sidebar-account"><a href="{{ route('admin.account') }}" class="account-identity"><span class="account-avatar">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span><span><strong>{{ auth()->user()->name }}</strong><small>{{ auth()->user()->role === 'admin' ? 'Administrator' : 'Content editor' }} ? My account</small></span></a><form method="POST" action="{{ route('admin.logout') }}">@csrf<button class="account-logout" aria-label="Sign out" title="Sign out"><x-admin-icon name="logout"/></button></form></div>
</details></aside>
<div class="admin-workspace">
<header class="admin-topbar"><div class="studio-breadcrumb"><span>Workspace</span><span aria-hidden="true">/</span><strong>{{ $title }}</strong></div><a href="{{ route('home') }}" target="_blank" rel="noopener" class="view-website">View website <x-admin-icon name="external"/></a></header>
<main id="admin-main" class="admin-main">
@if(session('success'))<div class="studio-alert studio-alert-success" role="status"><x-admin-icon name="check"/><p>{{ session('success') }}</p></div>@endif
@if($errors->any())<div class="studio-alert studio-alert-error" role="alert"><div><p class="font-semibold">Please check your changes.</p><ul class="mt-2 list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div></div>@endif
{{ $slot }}
</main><footer class="studio-footer"><span>Ideal Woman Community Based Organisation</span><span>Content studio <span aria-hidden="true">/</span> Greater Masaka, Uganda</span></footer>
</div></div>
<dialog data-image-library class="m-auto max-h-[85vh] w-[min(960px,94vw)] overflow-y-auto rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl" aria-labelledby="library-title">
<div class="mb-5 flex items-center justify-between gap-4"><h2 id="library-title" class="text-xl font-semibold">Choose a photograph</h2><button type="button" class="btn-secondary" data-close-library>Close</button></div><label for="library-search" class="sr-only">Find a photograph</label><input id="library-search" class="input mb-5" placeholder="Find a photograph by name"><div data-library-grid class="grid grid-cols-2 gap-4 md:grid-cols-3"></div><p data-library-empty hidden class="py-8 text-center text-slate-500">No photographs match. Try another word.</p>
</dialog></body></html>
