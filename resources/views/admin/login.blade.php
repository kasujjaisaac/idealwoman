<x-admin-auth-layout title="Admin sign in">
<form method="POST" action="{{ route('admin.login') }}" class="grid gap-5">@csrf
<div class="field"><label for="email">Email address</label><input class="input rounded-none" id="email" name="email" type="email" required autofocus autocomplete="username" value="{{ old('email') }}" maxlength="255"></div>
<div class="field"><label for="password">Password</label><input class="input rounded-none" id="password" name="password" type="password" required autocomplete="current-password" maxlength="1000" @if($errors->has('password')) aria-invalid="true" @endif></div>
<label class="flex cursor-pointer items-center gap-3 text-sm text-slate-600" for="remember"><input id="remember" name="remember" type="checkbox" value="1" class="h-4 w-4 rounded-none accent-blue-700" @checked(old('remember'))>Remember me</label>
<button class="btn w-full rounded-none" type="submit">Admin sign in</button>
</form>
<a class="mt-5 block text-center text-sm text-blue-700 hover:underline" href="{{ route('password.request') }}">Forgot password?</a>
</x-admin-auth-layout>
