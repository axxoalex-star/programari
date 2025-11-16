<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Afișează formularul de login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Procesează autentificarea
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Redirecționare în funcție de rol
            if ($user->role === 'admin') {
                return redirect()->intended('/admin/dashboard');
            } elseif ($user->role === 'doctor') {
                return redirect()->intended('/doctor/dashboard');
            } elseif ($user->role === 'receptie') {
                return redirect()->intended('/receptie/dashboard');
            } elseif ($user->role === 'assistant') {
                return redirect()->intended('/admin/dashboard'); // Asistenta va merge la admin
            }

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'Credențialele furnizate nu sunt corecte.',
        ])->onlyInput('email');
    }

    /**
     * Deconectare utilizator
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
