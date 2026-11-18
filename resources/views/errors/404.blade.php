<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Page not found | Ideal Woman</title>
    @vite('resources/css/app.css')
</head>
<body class="error-page">
    <main class="wrap error-content">
        <a class="brand" href="{{ route('home') }}" aria-label="Ideal Woman home"><img src="{{ asset('images/logo.png') }}" width="900" height="266" alt="Ideal Woman. Reaching out to you."></a>
        <p class="eyebrow">404 / Page not found</p>
        <h1>Let?s get you back on track.</h1>
        <p>This page may have moved, or the link may be out of date.</p>
        <div class="error-actions"><a class="button" href="{{ route('home') }}">Back to home</a><a class="button white" href="{{ route('contact') }}">Contact our team</a></div>
    </main>
</body>
</html>
