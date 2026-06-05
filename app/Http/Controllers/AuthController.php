<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin(): Response
    {
        $settings = SystemSetting::values(SystemSetting::kioskDefaults());

        return response()
            ->view('auth.login', [
                'loginBrand' => [
                    'logo_url' => $settings['login.logo_url'] ?? ($settings['admin.logo_url'] ?? null),
                    'title' => $settings['login.title'] ?? 'Visitor Management System',
                    'subtitle' => $settings['login.subtitle'] ?? 'Đăng nhập vào hệ thống vận hành',
                    'favicon_url' => $settings['app.favicon_url'] ?? ($settings['login.logo_url'] ?? null),
                ],
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');
    }

    public function csrfToken(Request $request): JsonResponse
    {
        $request->session()->regenerateToken();

        return response()
            ->json(['token' => csrf_token()])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
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
