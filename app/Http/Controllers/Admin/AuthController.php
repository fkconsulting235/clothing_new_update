<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->is_staff) {
            return redirect()->route('admin.home');
        }

        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = \App\Models\User::where('email', $credentials['username'])->first();

        if (! $user || ! $user->is_staff || ! Auth::attempt(['email' => $user->email, 'password' => $credentials['password']])) {
            return back()->withErrors(['username' => 'Usuario o contraseña incorrectos.'])->onlyInput('username');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('admin.home'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
