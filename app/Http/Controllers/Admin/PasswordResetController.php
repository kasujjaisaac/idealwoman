<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    public function request(): View
    {
        return view('admin.forgot-password');
    }

    public function send(Request $request): RedirectResponse
    {
        $data = $request->validate(['email' => ['required', 'email', 'max:255']]);
        Password::sendResetLink(['email' => $data['email'], 'role' => ['admin', 'editor']]);

        return back()->with('status', 'If this email has admin access, a password reset link has been sent. Please check your inbox and spam folder.');
    }

    public function edit(Request $request, string $token): View
    {
        $data = $request->validate(['email' => ['required', 'email', 'max:255']]);

        return view('admin.reset-password', ['token' => $token, 'email' => $data['email']]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'token' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:12', 'max:1000', 'confirmed'],
        ]);
        $status = Password::reset($data + ['role' => ['admin', 'editor']], function (User $user, string $password): void {
            $user->forceFill(['password' => Hash::make($password), 'remember_token' => Str::random(60)])->save();
            event(new PasswordReset($user));
        });
        if ($status !== Password::PasswordReset) {
            throw ValidationException::withMessages(['email' => 'This reset link is invalid or has expired. Please request a new one.']);
        }

        return to_route('login')->with('status', 'Password updated. Sign in with your new password.');
    }
}
