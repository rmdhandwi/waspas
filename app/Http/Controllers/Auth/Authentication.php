<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class Authentication extends Controller
{
    //
    public function loginPage()
    {
        return Inertia::render('Auth/Login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request)
    {
        // Validasi input (username dan password wajib diisi)
        $credentials = $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => 'Kolom username tidak boleh kosong',
            'password.required' => 'Kolom password tidak boleh kosong'
        ]);

        // Cek apakah kredensial valid
        if (Auth::attempt($credentials)) {
            $user = auth()->user(); // Ambil user yang berhasil login
            $userRole = $user->role; // Ambil role user

            // Regenerate session untuk menghindari session fixation
            $request->session()->regenerate();

            // Buat notifikasi sukses
            $notification = [
                'notif_status' => 'success',
                'notif_message' => 'Selamat Datang, ' . $user->username,
                'is_login' => true,
            ];

            // Alihkan berdasarkan role user
            if ($userRole !== null) {
                return redirect()->route('dashboard')->with($notification);
            } else {
                // Logout jika role tidak valid
                Auth::logout();
                $errorNotification = [
                    'notif_status' => 'error',
                    'notif_message' => 'Role tidak valid atau tidak diizinkan.',
                    'is_login' => false,
                ];
                return redirect()->route('login')->with($errorNotification);
            }
        }

        // Jika kredensial salah
        $errorNotification = [
            'notif_status' => 'error',
            'notif_message' => 'Username atau password salah!',
            'is_login' => false,
        ];

        return redirect()->route('login')->with($errorNotification);
    }

    public function destroy(Request $request)
    { // Logout pengguna dan hapus session
        Auth::logout();

        $request->session()->invalidate();  // Menghancurkan session
        $request->session()->regenerateToken();  // Mengganti token CSRF untuk keamanan

        // Redirect ke halaman login dengan notifikasi
        return redirect()->route('login')->with([
            'notif_status'  => 'success',
            'notif_message' => 'Anda telah logout.',
            'is_logout'    => true,
        ]);
    }
}
