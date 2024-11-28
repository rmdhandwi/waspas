<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\dataWarga as ModelsDataWarga;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DataWarga extends Controller
{
    //
    public function dataWarga()
    {
        $dataWarga = ModelsDataWarga::all();
        return Inertia::render('Admin/DataWarga/Index', ['data_warga' => $dataWarga]);
    }

    public function simpanDataWarga(Request $req)
    {
        $req->validate([
            "nomor_kk" => 'required|numeric|max:18|unique:data_warga,nomor_kk',
            "nama_kk" => 'required|unique:data_warga,nama_kk',
            "no_rt" => 'required|numeric',
            "no_rw" => 'required|numeric',
            "asal_suku" => 'required',
            "pekerjaan" => 'required',
            "lahan" => 'required',
            "legalitas_lahan" => 'required',
            "jmlh_penghasilan" => 'required',
            "jmlh_keluarga" => 'required|numeric',
            "agama" => 'required',
            "jenis_kelamin" => 'required',
            "sanitasi" => 'required',
            "j_kloset" => 'required',
            "t_limbah" => 'required',
            "akses_air_minum" => 'required',
            "status_rumah" => 'required',
            "struktur_bangunan" => 'required'
        ], [
            '*.required' => 'Kolom Wajib Diisi',
            'nomor_kk.unique' => 'Nomor KK Sudah Terdaftar',
            'nomor_kk.max' => 'Maksimal 18 karakter',
            'nama_kk.unique' => 'Nama Kepala Keluarga Sudah Terdaftar',
            '*.numeric' => 'Kolom Wajib Berisi Angka'
        ]);

        $insert = ModelsDataWarga::create([
            'nomor_kk' => $req->nomor_kk,
            'nama_kk' => $req->nama_kk,
            'provinsi' => $req->provinsi,
            'kabupaten' => $req->kabupaten,
            'kampung' => $req->kampung,
            "lahan" => $req->lahan,
            "legalitas_lahan" => $req->legalitas_lahan,
            "jmlh_penghasilan" => $req->jmlh_penghasilan,
            "jmlh_keluarga" => $req->jmlh_keluarga,
            'asal_suku' => $req->asal_suku,
            'pekerjaan' => substr($req->pekerjaan, 0, 8),
            'agama' => substr($req->agama, 0, 2),
            'jenis_kelamin' => $req->jenis_kelamin,
            'sanitasi' => $req->sanitasi,
            'j_kloset' => $req->j_kloset,
            't_limbah' => $req->t_limbah,
            'akses_air_minum' => $req->akses_air_minum,
            'status_rumah' => $req->status_rumah,
            'struktur_bangunan' => $req->struktur_bangunan,
            'rt' => $req->no_rt,
            'rw' => $req->no_rw,
            'created_at' => Carbon::now('Asia/Jayapura')
        ]);

        if ($insert) {
            return redirect()->route('admin.data_warga')->with([
                'notif_status' => 'success',
                'notif_message' => 'Berhasil menambahkan data rumah tangga warga!',
            ]);
        } else {
            return redirect()->route('admin.data_warga')->with([
                'notif_status' => 'error',
                'notif_message' => 'Gagal menambahkan data rumah tangga :( ',
            ]);
        }
    }

    public function updateDataWarga(Request $req)
    {
        // Validate the request
        $req->validate([
            "nomor_kk" => 'required|numeric|max:18|unique:data_warga,nomor_kk,' . $req->id,
            "nama_kk" => 'required|unique:data_warga,nama_kk,' . $req->id,
            "no_rt" => 'required|numeric',
            "no_rw" => 'required|numeric',
            "asal_suku" => 'required',
            "pekerjaan" => 'required',
            "agama" => 'required',
            "jenis_kelamin" => 'required',
            "lahan" => 'required',
            "legalitas_lahan" => 'required',
            "jmlh_penghasilan" => 'required',
            "jmlh_keluarga" => 'required|numeric',
            "sanitasi" => 'required',
            "j_kloset" => 'required',
            "t_limbah" => 'required',
            "akses_air_minum" => 'required',
            "status_rumah" => 'required',
            "struktur_bangunan" => 'required'
        ], [
            '*.required' => 'Kolom Wajib Diisi',
            'nomor_kk.unique' => 'Nomor KK Sudah Terdaftar',
            'nomor_kk.max' => 'Maksimal 18 karakter',
            'nama_kk.unique' => 'Nama Kepala Keluarga Sudah Terdaftar',
            '*.numeric' => 'Kolom Wajib Berisi Angka'
        ]);

        $currentData = ModelsDataWarga::find($req->id);

        $updatedData = [
            'nomor_kk' => $req->nomor_kk,
            'nama_kk' => $req->nama_kk,
            'provinsi' => $req->provinsi,
            'kabupaten' => $req->kabupaten,
            'kampung' => $req->kampung,
            'asal_suku' => $req->asal_suku,
            'jmlh_keluarga' => $req->jmlh_keluarga,
            'jmlh_penghasilan' => $req->jmlh_penghasilan,
            'lahan' => $req->lahan,
            "legalitas_lahan" => $req->legalitas_lahan,
            'pekerjaan' => substr($req->pekerjaan, 0, 8),
            'agama' => substr($req->agama, 0, 2),
            'jenis_kelamin' => $req->jenis_kelamin,
            'sanitasi' => $req->sanitasi,
            'j_kloset' => $req->j_kloset,
            't_limbah' => $req->t_limbah,
            'akses_air_minum' => $req->akses_air_minum,
            'status_rumah' => $req->status_rumah,
            'struktur_bangunan' => $req->struktur_bangunan,
            'rt' => $req->no_rt,
            'rw' => $req->no_rw,
        ];


        $hasChanges = array_diff_assoc($updatedData, $currentData->toArray());


        if (!empty($hasChanges)) {
            $update = $currentData->update($updatedData);
        } else {

            $update = true;
        }


        if ($update) {
            return redirect()->route('admin.data_warga')->with([
                'notif_status' => 'success',
                'notif_message' => 'Berhasil update data rumah tangga warga!',
            ]);
        } else {
            return redirect()->route('admin.data_warga')->with([
                'notif_status' => 'error',
                'notif_message' => 'Gagal update data rumah tangga :(',
            ]);
        }
    }


    public function hapusDataWarga(Request $req)
    {
        $update = ModelsDataWarga::find($req->id)->delete();
        if ($update) {
            return redirect()->route('admin.data_warga')->with([
                'notif_status' => 'success',
                'notif_message' => 'Berhasil hapus data rumah tangga warga!',
            ]);
        } else {
            return redirect()->route('admin.data_warga')->with([
                'notif_status' => 'error',
                'notif_message' => 'Gagal hapus data rumah tangga :( ',
            ]);
        }
    }
}
