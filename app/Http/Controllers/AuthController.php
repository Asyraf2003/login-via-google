<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required','string','max:255'],
            'email'    => ['required','email','max:255','unique:users,email'],
            'password' => ['required','string','min:6','confirmed'],
        ]);

        $user = User::create([
            'name'              => $data['name'],
            'email'             => $data['email'],
            'password'          => $data['password'], // auto-hash via casts
            'email_verified_at' => now(),
        ]);

        Auth::login($user, true);
        $request->session()->regenerate();
        // PERUBAHAN: Setelah Register, diarahkan ke '/home'
        return redirect()->intended('/home');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required','email'],
            'password' => ['required','string'],
        ]);

        // Cegah login manual untuk akun Google-only (password null)
        $user = User::where('email', $credentials['email'])->first();
        if ($user && is_null($user->password)) {
            return back()->withErrors([
                'email' => 'Akun ini terdaftar via Google dan belum memiliki password. Silakan masuk dengan Google.'
            ])->withInput();
        }

        if (Auth::attempt($credentials, remember: true)) {
            $request->session()->regenerate();
            // PERUBAHAN: Setelah Login manual, diarahkan ke '/home'
            return redirect()->intended('/home');
        }

        return back()->withErrors(['email' => 'Kredensial salah.'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login.show');
    }

    public function dashboard()
    {
        // PERUBAHAN: Mengembalikan view 'home' (welcome.blade.php)
        return view('home');
    }
}