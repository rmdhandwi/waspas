<?php

namespace App\Http\Controllers\super_admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class superAdminController extends Controller
{
    //
    public function Dashboard()
    {
        return Inertia::render('SuperAdmin/Dashboard');
    }

    public function UsersPage()
    {
        $user = User::where('role','admin')->select('username','nama','role','foto_profil')->get();
        return Inertia::render('SuperAdmin/Users', ['usersData' => $user ]);
    }

    public function tambahPengguna(Request $req)
    {
        $req->validate([
            'username' => 'required|unique:users',
            'nama' => 'required',
            'tgl_lahir' => 'required',
            'jkel' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required',
            'no_telp' => 'required|unique:users',
            'role' => 'required',
        ]);

        $insert = User::create([
            'username' => $req->username,
            'nama' => $req->nama,
            'tgl_lahir' => Carbon::parse($req->tgl_lahir)->format('Y-m-d'),
            'jkel' => $req->jkel,
            'email' => $req->email,
            'password' => Hash::make($req->password),
            'no_telp' => $req->no_telp,
            'role' => $req->role,
            'created_at' =>  Carbon::now('Asia/Jayapura')
        ]);

        if($insert)
        {
            return redirect()->back()->with([
                'notif_status' => 'success',
                'notif_message' => 'Berhasil menambahkan pengguna!',
            ]);
        }
        else
        {
            return redirect()->back()->with([
                'notif_status' => 'failed',
                'notif_message' => 'Gagal menambahkan pengguna :( ',
            ]);
        }
    }
}
