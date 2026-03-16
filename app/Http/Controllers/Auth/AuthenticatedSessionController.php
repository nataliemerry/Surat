<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    public function create(): Response
    {
           // Simpan URL sebelumnya jika bukan halaman login
           $previousUrl = url()->previous();
           if ($previousUrl && !str_contains($previousUrl, '/login')) {
              session(['previous_url' => $previousUrl]);
           }
           return Inertia::render('Auth/Login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

            // Redirect ke previous_url jika ada dan bukan halaman login
            $previousUrl = session('previous_url');
            if ($previousUrl && !str_contains($previousUrl, '/login')) {
                session()->forget('previous_url');
                return redirect($previousUrl);
            }
            return redirect()->intended(route('home'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

           return redirect(url()->previous(route('home')));
    }
}
