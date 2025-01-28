<?php

namespace App\Http\Controllers\super_admin;

use App\Http\Controllers\Controller;
use App\Models\User as ModelsUser;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class user extends Controller
{
    public function userpage()
    {

        $dataUser = AuthUser::all();

        return Inertia::render(('SuperAdmin/Users'), [
            'users' => $dataUser
        ]);
    }

    public function tambahPengguna(Request $req)
    {
        $req->validate(
            [
                'nama' => 'required',
                'username' => 'required|unique:users',
                'password' => 'required',
                'role' => 'required',
            ],
            [
                '*.required' => 'Kolom wajib diisi',
                'username.unique' => 'Username telah digunakan'
            ]
        );

        $insert = ModelsUser::create([
            'nama' => $req->nama,
            'username' => $req->username,
            'password' => Hash::make($req->password),
            'role' => $req->role,
        ]);

        if ($insert) {
            return redirect()->back()->with([
                'notif_status' => 'success',
                'notif_message' => 'Berhasil menambahkan pengguna!',
            ]);
        } else {
            return redirect()->back()->with([
                'notif_status' => 'failed',
                'notif_message' => 'Gagal menambahkan pengguna :(',
            ]);
        }
    }

    public function register(Request $req)
    {
        $req->validate(
            [
                'nama' => 'required',
                'username' => 'required|unique:users',
                'password' => 'required',
            ],
            [
                '*.required' => 'Kolom wajib diisi',
                'username.unique' => 'Username telah digunakan'
            ]
        );

        $insert = ModelsUser::create([
            'nama' => $req->nama,
            'username' => $req->username,
            'password' => Hash::make($req->password),
            'role' => 'warga',
        ]);

        if ($insert) {
            return redirect()->back()->with([
                'notif_status' => 'success',
                'notif_message' => 'Berhasil membuat akaun. Silahkan Login!',
            ]);
        } else {
            return redirect()->back()->with([
                'notif_status' => 'failed',
                'notif_message' => 'Gagal membuat akun. Daftarkan akun kembali!',
            ]);
        }
    }

    public function updateUser(Request $req, $id)
    {
        $user = ModelsUser::find($id);

        if (!$user) {
            return redirect()->back()->with([
                'notif_status' => 'failed',
                'notif_message' => 'Pengguna tidak ditemukan.',
            ]);
        }

        // Validasi data termasuk kondisi terkait password
        $req->validate([
            'nama' => 'required',
            'username' => "required|unique:users,username,{$id}",
            'password' => $req->password_baru ? 'required' : 'nullable',
            'password_baru' => $req->password ? 'required' : 'nullable',
            'role' => 'required',
        ], [
            '*.required' => 'Kolom wajib diisi',
            'username.unique' => 'Username telah digunakan',
            'password_baru.required' => 'Password baru wajib diisi jika password lama diisi.',
        ]);

        // Validasi Password Lama
        if ($req->password) {
            if (!Hash::check($req->password, $user->password)) {
                return redirect()->back()->withErrors([
                    'password' => 'Password lama tidak cocok.',
                ])->withInput();
            }

            // Validasi Password Baru
            if ($req->password_baru && Hash::check($req->password_baru, $user->password)) {
                return redirect()->back()->withErrors([
                    'password_baru' => 'Password baru tidak boleh sama dengan password lama.',
                ])->withInput();
            }
        }

        // Cek perubahan data
        $hasChanges = false;
        if ($user->nama !== $req->nama) $hasChanges = true;
        if ($user->username !== $req->username) $hasChanges = true;
        if ($user->role !== $req->role) $hasChanges = true;
        if ($req->password_baru) $hasChanges = true;

        // Update data pengguna
        $user->nama = $req->nama;
        $user->username = $req->username;
        $user->role = $req->role;

        if ($req->password_baru) {
            $user->password = Hash::make($req->password_baru);
        }

        // dd(auth()->user()->id == $id && $hasChanges);

        $user->save();

        // Logout jika pengguna yang diubah adalah pengguna yang sedang login dan ada perubahan
        if (Auth::user()->id == $id && $hasChanges) {
            Auth::logout();

            $req->session()->invalidate();  // Menghancurkan session
            $req->session()->regenerateToken();  // Mengganti token CSRF untuk keamanan

            return redirect()->route('login')->with([
                'notif_status'  => 'success',
                'notif_message' => 'Data pengguna berhasil diubah. Silakan login kembali.',
                'is_logout'    => true,
            ]);
        }

        return redirect()->back()->with([
            'notif_status' => 'success',
            'notif_message' => 'Pengguna berhasil diperbarui!',
        ]);
    }

    public function hapusPengguna($id)
    {

        if (Auth::user()->id == $id) {
            return back()->with([
                'notif_status' => 'failed',
                'notif_message' => 'Tidak dapat menghapus pengguna yang sedang login',
            ]);
        }
        $user = ModelsUser::findOrFail($id);
        $userDelete = $user->delete();

        if ($userDelete) {
            return back()->with([
                'notif_status' => 'success',
                'notif_message' => 'Berhasil menghapus pengguna!',
            ]);
        } else {
            return back()->with([
                'notif_status' => 'failed',
                'notif_message' => 'Gagal menghapus pengguna :(',
            ]);
        }
    }
}
