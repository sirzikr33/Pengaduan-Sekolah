<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login_id' => 'required',
            'password' => 'required',
        ], [
            'login_id.required' => 'Email atau NISN wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $loginId  = $request->input('login_id');
        $password = $request->input('password');
        $remember = $request->boolean('remember');

        $isEmail = filter_var($loginId, FILTER_VALIDATE_EMAIL);
        $attempt = false;

        if ($isEmail) {
            // Login langsung dengan kolom email (biasanya Admin)
            $attempt = Auth::attempt(['email' => $loginId, 'password' => $password], $remember);
        } else {
            // Jika bukan email, anggap sebagai NISN (Siswa)
            $siswa = \App\Models\Siswa::where('nisn', $loginId)->first();
            if ($siswa) {
                $user = \App\Models\User::where('siswa_id', $siswa->id)->first();
                if ($user) {
                    // Autentikasi menggunakan email user terkait yang terdaftar
                    $attempt = Auth::attempt(['email' => $user->email, 'password' => $password], $remember);
                }
            }
        }

        if ($attempt) {
            $request->session()->regenerate();
            $user = Auth::user();
            return $this->redirectByRole($user)
                ->with('success', 'Selamat datang, ' . $user->name . '!');
        }

        return back()
            ->withInput($request->only('login_id'))
            ->withErrors(['login_id' => 'Kombinasi akun dan password tidak sesuai.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')
            ->with('success', 'Anda berhasil logout.');
    }
    private function redirectByRole($user)
    {
        return match($user->role) {
            'siswa' => redirect()->route('siswa.dashboard'),
            default => redirect()->route('dashboard'),
        };
    }
}
