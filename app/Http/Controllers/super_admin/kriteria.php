<?php

namespace App\Http\Controllers\super_admin;

use App\Http\Controllers\Controller;
use App\Models\Kriteria as ModelsKriteria;
use App\Models\SubKriteria;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class kriteria extends Controller
{
    //
    public function kriteria_page()
    {   
        $dataKriteria = ModelsKriteria::all();
        $dataSubKriteria = SubKriteria::all();
        return Inertia::render('SuperAdmin/Kriteria/Kriteria', ['dataKriteria' => $dataKriteria, 'dataSubKriteria' => $dataSubKriteria]);
    }
    public function tambah_kriteria(Request $req)
    {
        $req->validate([
            'jenis' => 'required',
            'nama' => 'required',
            'nilai_bobot' => 'required',
        ]);

        $insert = ModelsKriteria::create([
            'jenis' => $req->jenis,
            'nama' => $req->nama,
            'nilai_bobot' => $req->nilai_bobot,
            'created_at' => Carbon::now('Asia/Jayapura')
        ]);

        if($insert)
        {
            return redirect()->route('super_admin.kriteria')->with([
                'notif_status' => 'success',
                'notif_message' => 'Berhasil menambahkan kriteria!',
            ]);
        }
        else
        {
            return redirect()->route('super_admin.kriteria')->with([
                'notif_status' => 'failed',
                'notif_message' => 'Gagal menambahkan kriteria :( ',
            ]);
        }
    }
}
