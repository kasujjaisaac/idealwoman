<x-admin-auth-layout title="Choose a new password">
<p class="mb-6 text-sm leading-7 text-slate-600">Choose a new password with at least 12 characters.</p>
<form method="POST" action="{{ route('password.update') }}" class="grid gap-5">@csrf
<input type="hidden" name="token" value="{{ $token }}"><input type="hidden" name="email" value="{{ $email }}">
<div class="field"><label for="password">New password</label><input class="input rounded-none" id="password" name="password" type="password" required autofocus autocomplete="new-password" minlength="12" maxlength="1000"></div>
<div class="field"><label for="password_confirmation">Confirm new password</label><input class="input rounded-none" id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" minlength="12" maxlength="1000"></div>
<button class="btn w-full rounded-none" type="submit">Reset password</button>
</form><a class="mt-6 block text-center text-sm text-blue-700 hover:underline" href="{{ route('password.request') }}">Request a new reset link</a>
</x-admin-auth-layout>
