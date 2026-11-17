<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function create(): View|RedirectResponse
    {
        return (Auth::check() && Auth::user()->can('manage-content')) ? to_route('admin.dashboard') : view('admin.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['email' => ['required', 'email', 'max:255'], 'password' => ['required', 'string', 'max:1000'], 'remember' => ['sometimes', 'boolean']]);
        if (! Auth::attempt(['email' => $data['email'], 'password' => $data['password']], $request->boolean('remember'))) {
            throw ValidationException::withMessages(['email' => 'These credentials do not match our records.']);
        }
        if (! in_array($request->user()->role, ['admin', 'editor'], true)) {
            Auth::logout();
            throw ValidationException::withMessages(['email' => 'This account does not have access to the admin panel.']);
        }
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('login');
    }
}
