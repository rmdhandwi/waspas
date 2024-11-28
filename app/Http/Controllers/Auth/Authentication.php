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
        // Validate the input credentials (excluding role)
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);


        if (Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password']])) {

            // Cek role
            $userRole = auth()->user()->role;

            $notification = [
                'notif_status' => 'success',
                'notif_message' => 'Selamat Datang, ' . auth()->user()->username,
                'is_login' => true,
            ];

            $request->session()->regenerate();


            switch ($userRole) {
                case 'super_admin':
                    return redirect()->route('super_admin.dashboard')->with($notification);
                case 'admin':
                    return redirect()->route('admin.dashboard')->with($notification);
                default:
                    Auth::logout();
                    $errorNotification = [
                        'notif_status' => 'error',
                        'notif_message' => 'Role tidak valid atau tidak diizinkan.',
                        'is_login' => true,
                    ];
                    return redirect()->route('login')->with($errorNotification);
            }
        }

        $errorNotification = [
            'notif_status' => 'error',
            'notif_message' => 'Username atau password salah!',
            'is_login' => true,
        ];

        // Redirect back to the login page with an error notification
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
