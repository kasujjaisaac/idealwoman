@props(['title' => 'Admin sign in'])
<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex, nofollow"><title>{{ $title }} | Ideal Woman</title><link rel="icon" href="{{ $site->media('logo', asset('images/logo.png')) }}">@vite(['resources/css/admin.css'])</head>
<body class="flex min-h-svh items-center justify-center bg-slate-50 px-5 py-12 font-sans text-slate-800">
<main class="w-full max-w-sm rounded-none border border-slate-200 bg-white p-7 shadow-sm sm:p-9">
<a href="{{ route('home') }}" class="mx-auto mb-8 block w-52 max-w-full"><img src="{{ $site->media('logo', asset('images/logo.png')) }}" width="900" height="266" alt="Ideal Woman" class="h-auto w-full"></a>
<h1 class="sr-only">{{ $title }}</h1>
@if(session('status'))<p class="mb-6 rounded-none bg-emerald-50 p-4 text-sm leading-6 text-emerald-800" role="status">{{ session('status') }}</p>@endif
@if($errors->any())<div class="mb-6 rounded-none bg-red-50 p-4 text-sm leading-6 text-red-800" role="alert">{{ $errors->first() }}</div>@endif
{{ $slot }}
</main>
</body></html>
