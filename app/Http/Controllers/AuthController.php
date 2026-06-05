<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin(): Response
    {
        return response()
            ->view('auth.login')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Thong tin dang nhap khong dung.']);
        }

        if (! Auth::user()?->is_active) {
            Auth::logout();

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Tai khoan da bi khoa. Vui long lien he quan tri he thong.']);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Da dang xuat thanh cong.');
    }
}
