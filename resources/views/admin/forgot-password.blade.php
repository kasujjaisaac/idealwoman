<x-admin-auth-layout title="Reset your password">
<p class="mb-6 text-sm leading-7 text-slate-600">Enter your account email to receive a password reset link.</p>
<form method="POST" action="{{ route('password.email') }}" class="grid gap-5">@csrf
<div class="field"><label for="email">Email address</label><input class="input rounded-none" id="email" name="email" type="email" required autofocus autocomplete="username" value="{{ old('email') }}" maxlength="255"></div>
<button class="btn w-full rounded-none" type="submit">Send reset link</button>
</form><a class="mt-6 block text-center text-sm text-blue-700 hover:underline" href="{{ route('login') }}">Back to sign in</a>
</x-admin-auth-layout>
