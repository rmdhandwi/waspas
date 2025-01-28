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
            'nama' => 'required',
            'nilai_bobot' => 'required|numeric|max:5',
            'id_relasi' => 'required',
        ], [
            '*.required' => 'Kolom Wajib Diisi',
            '*.numeric' => 'Kolom Wajib Berupa Angka',
            'nilai_bobot.max' => 'Nilai Maksimal 5',
        ]);

        // Mengubah nama kriteria menjadi huruf kecil dan mengganti spasi dengan garis bawah
        $namaSubKriteriaFormat = strtolower(str_replace(' ', '_', $req->nama));

        $insert = ModelsSubKriteria::create([
            'nama_subkriteria' => $namaSubKriteriaFormat,
            'nilai' => $req->nilai_bobot,
            'kriteria_id' => $req->id_relasi,
        ]);

        if ($insert) {
            return redirect()->back()->with([
                'notif_status' => 'success',
                'notif_message' => 'Berhasil menambahkan sub kriteria!',
                'is_kriteria' => false
            ]);
        } else {
            return redirect()->back()->with([
                'notif_status' => 'failed',
                'notif_message' => 'Gagal menambahkan sub kriteria :( ',
                'is_kriteria' => false
            ]);
        }
    }

    public function update_subkriteria(Request $req)
    {
        $req->validate([
            'nama_sub' => 'required',
            'nilai_bobot' => 'required|numeric|max:5',
            'id_relasi' => 'required',
        ], [
            '*.required' => 'Kolom Wajib Diisi',
            '*.numeric' => 'Kolom Wajib Berupa Angka',
            'nilai_bobot.max' => 'Nilai maksimal 5',
        ]);

        // Mengubah nama kriteria menjadi huruf kecil dan mengganti spasi dengan garis bawah
        $namaSubKriteriaFormat = strtolower(str_replace(' ', '_', $req->nama_sub));

        $updateData = [
            'nama_subkriteria' => $namaSubKriteriaFormat,
            'nilai' => $req->nilai_bobot,
            'kriteria_id' => $req->id_relasi,
        ];

        $insert = ModelsSubKriteria::where('id', $req->id)->update($updateData);

        if ($insert) {
            return redirect()->route('super_admin.kriteria')->with([
                'notif_status' => 'success',
                'notif_message' => 'Berhasil update sub kriteria!',
            ]);
        } else {
            return redirect()->route('super_admin.kriteria')->with([
                'notif_status' => 'failed',
                'notif_message' => 'Gagal update sub kriteria :( ',
            ]);
        }
    }

    public function hapus_subkriteria(Request $req)
    {
        $user = ModelsSubKriteria::find($req->id)->delete();

        if ($user) {
            return redirect()->route('super_admin.kriteria')->with([
                'notif_status' => 'success',
                'notif_message' => 'Berhasil menghapus sub kriteria!',
            ]);
        } else {
            return redirect()->route('super_admin.kriteria')->with([
                'notif_status' => 'failed',
                'notif_message' => 'Gagal menghapus sub kriteria :(',
            ]);
        }
    }
}
