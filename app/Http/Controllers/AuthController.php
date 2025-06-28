<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // Tambahkan ini
use Illuminate\Support\Facades\Auth; // Tambahkan ini

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([ // Perbaiki dari Validata ke validate
            'email' => 'required|email',
            'password' => 'required',
        ]);
        
        $user = User::where('email', $request->email)->first();
        
        if ($user && password_verify($request->password, $user->password)) {
            Auth::login($user);
            return redirect()->route('mahasiswa')->with('success', 'Login successful');
        }

        return redirect()->back()->withErrors(['email' => 'Email atau password salah'])->withInput();
    } // Tambahkan penutup kurung kurawal untuk method login()

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login')->with('success', 'Logout successful'); // Hapus slash di 'login'
    }
}