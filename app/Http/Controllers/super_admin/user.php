<?php

namespace App\Http\Controllers\super_admin;

use App\Http\Controllers\Controller;
use App\Models\User as ModelsUser;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class user extends Controller
{
    //
    
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

        $insert = ModelsUser::create([
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

    public function viewPengguna(Request $req)
    {
        $user = ModelsUser::find($req->id);
        return Inertia::render('SuperAdmin/Users/View', ['userData' => $user]);
    }
    
    public function updatePengguna(Request $req)
    {
        $updateData = [
            'username' => $req->username,
            'nama' => $req->nama,
            'tgl_lahir' => Carbon::parse($req->tgl_lahir)->format('Y-m-d'),
            'jkel' => $req->jkel,
            'email' => $req->email,
            'no_telp' => $req->no_telp,
            'alamat' => $req->alamat,
        ];

        $insert = ModelsUser::where('id', $req->id)->update($updateData);

        if($insert)
        {
            return redirect()->route('super_admin.pengguna')->with([
                'notif_status' => 'success',
                'notif_message' => 'Berhasil update pengguna!',
            ]);
        }
        else
        {
            return redirect()->route('super_admin.pengguna')->with([
                'notif_status' => 'failed',
                'notif_message' => 'Gagal mengupdate pengguna :( ',
            ]);
        }
    }
    public function hapusPengguna(Request $req)
    {
        $user = ModelsUser::find($req->id)->delete();

        if($user)
        {
            return back()->with([
                'notif_status' => 'success',
                'notif_message' => 'Berhasil menghapus pengguna!',
            ]);
        }
        else 
        {
            return back()->with([
                'notif_status' => 'failed',
                'notif_message' => 'Gagal menghapus pengguna :(',
            ]);
        }
        // dd($user);
    }
}
