<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users', ['users' => User::query()->orderBy('name')->orderBy('id')->paginate(20)]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'email' => ['required', 'email', 'max:254', Rule::unique('users')], 'password' => ['required', 'string', 'min:12', 'confirmed'], 'role' => ['required', Rule::in(['admin', 'editor'])]]);
        $user = new User(collect($data)->only(['name', 'email', 'password'])->all());
        $user->role = $data['role'];
        $user->save();

        return back()->with('success', 'Team member created.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate(['role' => ['required', Rule::in(['admin', 'editor', 'user'])]]);
        abort_if($user->id === $request->user()->id, 422, 'You cannot change your own access.');
        $user->role = $data['role'];
        $user->save();
        DB::table('sessions')->where('user_id', $user->id)->delete();

        return back()->with('success', 'Access updated.');
    }

    public function password(Request $request): RedirectResponse
    {
        $data = $request->validate(['current_password' => ['required', 'current_password'], 'password' => ['required', 'string', 'min:12', 'confirmed']]);
        $request->user()->update(['password' => $data['password']]);
        DB::table('sessions')->where('user_id', $request->user()->id)->where('id', '!=', $request->session()->getId())->delete();
        $request->session()->regenerate();

        return back()->with('success', 'Your password has been changed.');
    }

    public function profile(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:254', Rule::unique('users')->ignore($request->user()->id)],
            'current_password' => ['required', 'current_password'],
        ]);
        $request->user()->update(collect($data)->only(['name', 'email'])->all());

        return back()->with('success', 'Your profile has been updated.');
    }

    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        abort_if($user->id === $request->user()->id, 422, 'Use My account to change your own password.');
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ]);
        $user->update(['password' => $data['password']]);
        DB::table('sessions')->where('user_id', $user->id)->delete();

        return back()->with('success', 'Team member password reset. Share the new password privately.');
    }
}
