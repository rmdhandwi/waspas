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
        // $jenis = ModelsKriteria::generateJenisKriteria();
        // dd($jenis);
        $dataKriteria = ModelsKriteria::all();
        $dataSubKriteria = SubKriteria::join('kriteria','sub_kriteria.id_kriteria', '=','kriteria.id')->select('kriteria.jenis','sub_kriteria.*')->get();

        return Inertia::render('SuperAdmin/Kriteria/Kriteria', ['dataKriteria' => $dataKriteria, 'dataSubKriteria' => $dataSubKriteria]);
    }

    public function tambah_kriteria(Request $req)
    {
        // dd($req);
        $req->validate([
            'nama' => 'required',
            'nilai_bobot' => 'required|numeric',
        ],
        [
            '*.required' => 'Kolom Wajib Diisi',
            '*.numeric' => 'Kolom Wajib Berupa Angka',
        ]);

        $insert = ModelsKriteria::create([
            'jenis' => ModelsKriteria::generateJenisKriteria(),
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

    public function view_kriteria(Request $req)
    {
        $dataKriteria = ModelsKriteria::find($req->id);
        return Inertia::render('SuperAdmin/Kriteria/View', ['dataKriteria' => $dataKriteria]);
    }

    public function update_kriteria(Request $req)
    {
        $req->validate(
            [
                'nama' => 'required',
                'nilai_bobot' => 'required|numeric',
            ],
            [
                '*.required' => 'Kolom Wajib Diisi',
                '*.numeric' => 'Kolom Wajib Berupa Angka',
            ]
        );

        $updateData = [
            'nama' => $req->nama,
            'nilai_bobot' => $req->nilai_bobot,
        ];

        $insert = ModelsKriteria::where('id', $req->id)->update($updateData);

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

    public function hapus_kriteria(Request $req)
    {
        $user = ModelsKriteria::find($req->id)->delete();

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
