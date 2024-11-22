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
            "nomor_kk"=> 'required|unique:data_warga,nomor_kk',
            "nama_kk"=> 'required|unique:data_warga,nama_kk',
            "no_rt"=> 'required|numeric',
            "no_rw"=> 'required|numeric',
            "asal_suku"=> 'required',
            "pekerjaan"=> 'required',
            "agama"=> 'required',
            "jenis_kelamin"=> 'required',
            "sanitasi"=> 'required',
            "j_kloset"=> 'required',
            "t_limbah"=> 'required',
            "akses_air_minum"=> 'required',
            "status_rumah"=> 'required',
            "struktur_bangunan"=> 'required'
        ], [
            '*.required' => 'Kolom Wajib Diisi',
            'nomor_kk.unique' => 'Nomor KK Sudah Terdaftar',
            'nama_kk.unique' => 'Nama Kepala Keluarga Sudah Terdaftar',
            '*.numeric' => 'Kolom Wajib Berisi Angka'
            // 'nomor_kk.required' => 'Harap isi Nomor KK',
            // 'nomor_kk.unique' => 'Nomor KK Telah Terdaftar',
            // 'nama_kk.required' => 'Harap isi Nama Kepala Keluarga',
            // 'no_rt.required' => 'Harap isi Nomor RT',
            // 'no_rt.numeric' => 'RT harus berupa angka',
            // 'no_rw.required' => 'Harap isi Nomor RW',
            // 'no_rw.numeric' => 'RW harus berupa angka',
            // 'asal_suku.required' => 'Harap isi Asal Suku',
            // 'pekerjaan.required' => 'Harap isi Pekerjaan',
            // 'agama.required' => 'Harap isi Agama',
            // 'jenis_kelamin.required' => 'Harap isi Jenis Kelamin',
            // 'sanitasi.required' => 'Harap isi Sanitasi',
            // 'j_kloset.required' => 'Harap isi Jenis Kloset',
            // 't_limbah.required' => 'Harap isi Tempat Pembuangan Limbah Tinja',
            // 'akses_air_minum.required' => 'Harap isi Akses Air Minum',
            // 'status_rumah.required' => 'Harap isi Status Kepemilikan Rumah',
            // 'struktur_bangunan.required' => 'Harap isi Struktur Bangunan',
        ]);

        $insert = ModelsDataWarga::create([
            'nomor_kk' => $req->nomor_kk,
            'nama_kk' => $req->nama_kk,
            'provinsi' => $req->provinsi,
            'kabupaten' => $req->kabupaten,
            'kampung' => $req->kampung,
            'asal_suku' => $req->asal_suku,
            'pekerjaan' => substr($req->pekerjaan,0,8),
            'agama' => substr($req->agama,0,2),
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

        if($insert)
        {
            return redirect()->route('admin.data_warga')->with([
                'notif_status' => 'success',
                'notif_message' => 'Berhasil menambahkan data rumah tangga warga!',
            ]);
        }
        else
        {
            return redirect()->route('admin.data_warga')->with([
                'notif_status' => 'error',
                'notif_message' => 'Gagal menambahkan data rumah tangga :( ',
            ]);
        }
    }

    public function updateDataWarga(Request $req)
    {
        $req->validate([
            "nomor_kk"=> 'required|unique:data_warga,nomor_kk,'. $req->id,
            "nama_kk"=> 'required|unique:data_warga,nama_kk,'. $req->id,
            "no_rt"=> 'required|numeric',
            "no_rw"=> 'required|numeric',
            "asal_suku"=> 'required',
            "pekerjaan"=> 'required',
            "agama"=> 'required',
            "jenis_kelamin"=> 'required',
            "sanitasi"=> 'required',
            "j_kloset"=> 'required',
            "t_limbah"=> 'required',
            "akses_air_minum"=> 'required',
            "status_rumah"=> 'required',
            "struktur_bangunan"=> 'required'
        ], [
            '*.required' => 'Kolom Wajib Diisi',
            'nomor_kk.unique' => 'Nomor KK Sudah Terdaftar',
            'nama_kk.unique' => 'Nama Kepala Keluarga Sudah Terdaftar',
            '*.numeric' => 'Kolom Wajib Berisi Angka'
        ]);

        $update = ModelsDataWarga::where('id', $req->id)->update([
            'nomor_kk' => $req->nomor_kk,
            'nama_kk' => $req->nama_kk,
            'provinsi' => $req->provinsi,
            'kabupaten' => $req->kabupaten,
            'kampung' => $req->kampung,
            'asal_suku' => $req->asal_suku,
            'pekerjaan' => substr($req->pekerjaan,0,8),
            'agama' => substr($req->agama,0,2),
            'jenis_kelamin' => $req->jenis_kelamin,
            'sanitasi' => $req->sanitasi,
            'j_kloset' => $req->j_kloset,
            't_limbah' => $req->t_limbah,
            'akses_air_minum' => $req->akses_air_minum,
            'status_rumah' => $req->status_rumah,
            'struktur_bangunan' => $req->struktur_bangunan,
            'rt' => $req->no_rt,
            'rw' => $req->no_rw,
        ]);

        if($update)
        {
            return redirect()->route('admin.data_warga')->with([
                'notif_status' => 'success',
                'notif_message' => 'Berhasil update data rumah tangga warga!',
            ]);
        }
        else
        {
            return redirect()->route('admin.data_warga')->with([
                'notif_status' => 'error',
                'notif_message' => 'Gagal update data rumah tangga :( ',
            ]);
        }
    }

    public function hapusDataWarga(Request $req)
    {
        $update = ModelsDataWarga::find($req->id)->delete();
        if($update)
        {
            return redirect()->route('admin.data_warga')->with([
                'notif_status' => 'success',
                'notif_message' => 'Berhasil hapus data rumah tangga warga!',
            ]);
        }
        else
        {
            return redirect()->route('admin.data_warga')->with([
                'notif_status' => 'error',
                'notif_message' => 'Gagal hapus data rumah tangga :( ',
            ]);
        }
    }
}
