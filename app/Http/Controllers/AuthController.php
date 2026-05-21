<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login', ['mode' => 'login']);
    }

    public function showRegister()
    {
        return view('auth.login', ['mode' => 'register']);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();
            return redirect('/dashboard');
        }

        return back()->withErrors(['email' => 'Email ou mot de passe incorrect.'])->withInput($request->only('email'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'nom' => 'required|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:4|confirmed',
        ], [
            'nom.required' => 'Le nom est obligatoire.',
            'email.unique' => 'Cet email est deja utilise.',
            'password.min' => 'Le mot de passe doit faire au moins 4 caracteres.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
        ]);

        User::create([
            'name' => $request->nom,
            'nom' => $request->nom,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        return redirect('/login')->with('success', 'Compte cree avec succes ! Vous pouvez vous connecter.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
