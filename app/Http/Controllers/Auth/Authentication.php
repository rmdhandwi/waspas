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
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
            'role' => ['required'],
        ]);
        
        if(Auth::attempt($credentials))
        {
            $notification = [
                'notif_status' => 'success',
                'message' => 'Selamat Datang, '.auth()->user()->nama,
                'notif_show' => true,
            ];

            $request->session()->regenerate();

            switch($request->role)
            {
                case 'super_admin' : return redirect()->route('super_admin.dashboard')->with($notification);
                break;
                case 'admin' : return redirect()->route('admin.dashboard')->with($notification);
                break;
            };
        }
        else
        {
            return response('terjadi kesalahan!',200);
        }
    }

    public function destroy()
    {
        Auth::logout();
        Session::flush();
        return redirect()->route('login');
    }
}
