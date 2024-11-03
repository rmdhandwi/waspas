<?php

namespace App\Http\Controllers\super_admin;

use App\Http\Controllers\Controller;
use App\Models\SubKriteria as ModelsSubKriteria;
use Carbon\Carbon;
use Illuminate\Http\Request;

class subKriteria extends Controller
{
    //
    public function tambah_subKriteria(Request $req)
    {
        $req->validate([
            'jenis' => 'required',
            'nama' => 'required',
            'nilai_bobot' => 'required',
            'id_relasi' => 'required',
        ]);

        $insert = ModelsSubKriteria::create([
            'jenis_sub' => $req->jenis,
            'nama_sub' => $req->nama,
            'nilai_bobot' => $req->nilai_bobot,
            'id_kriteria' => $req->id_relasi,
            'created_at' => Carbon::now('Asia/Jayapura')
        ]);

        if($insert)
        {
            return redirect()->back()->with([
                'notif_status' => 'success',
                'notif_message' => 'Berhasil menambahkan sub kriteria!',
                'is_kriteria' => false
            ]);
        }
        else
        {
            return redirect()->back()->with([
                'notif_status' => 'failed',
                'notif_message' => 'Gagal menambahkan sub kriteria :( ',
                'is_kriteria' => false
            ]);
        }
    }

    public function update_subkriteria(Request $req)
    {
        $updateData = [
            'jenis_sub' => $req->jenis_sub,
            'nama_sub' => $req->nama_sub,
            'nilai_bobot' => $req->nilai_bobot,
            'id_kriteria' => $req->id_relasi,
        ];

        $insert = ModelsSubKriteria::where('id', $req->id)->update($updateData);

        if($insert)
        {
            return redirect()->route('super_admin.kriteria')->with([
                'notif_status' => 'success',
                'notif_message' => 'Berhasil update kriteria!',
            ]);
        }
        else
        {
            return redirect()->route('super_admin.kriteria')->with([
                'notif_status' => 'failed',
                'notif_message' => 'Gagal update kriteria :( ',
            ]);
        }
    }

    public function hapus_subkriteria(Request $req)
    {
        $user = ModelsSubKriteria::find($req->id)->delete();

        if($user)
        {
            return redirect()->route('super_admin.kriteria')->with([
                'notif_status' => 'success',
                'notif_message' => 'Berhasil menghapus kriteria!',
            ]);
        }
        else 
        {
            return redirect()->route('super_admin.kriteria')->with([
                'notif_status' => 'failed',
                'notif_message' => 'Gagal menghapus kriteria :(',
            ]);
        }
    }
}
