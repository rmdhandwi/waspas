<?php

namespace App\Http\Controllers;

use App\Models\dataWarga;
use App\Models\Kriteria;
use App\Models\periode;
use App\Models\SubKriteria;
use App\Models\warga as ModelsWarga;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class Warga extends Controller
{
    public function wargaPage()
    {
        $dataPeriode = periode::all();
        $dataKriteria = Kriteria::all();
        $dataSubkriteria = SubKriteria::all();
        $dataWarga = ModelsWarga::with(['periode' => function ($query) {
            $query->select('id', 'tahun');
        }])->get();
        return Inertia::render('DataWarga/WargaPage', [
            'periode' => $dataPeriode,
            'kriteria' => $dataKriteria,
            'subkriteria' => $dataSubkriteria,
            'warga' => $dataWarga
        ]);
    }

    public function store(Request $request)
    {
        // Validasi data warga
        // dd($request->all());
        $request->validate(
            [
                'nomor_kk' => 'required|unique:warga,nomor_kk|numeric',
                'nama_kk' => 'required',
                'periode_id' => 'required',
                'provinsi' => 'required',
                'kabupaten' => 'required',
                'kampung' => 'required',
                'rt' => 'required|numeric',
                'rw' => 'required|numeric',
                'pekerjaan' => 'required',
                'agama' => 'required',
                'jenis_kelamin' => 'required',
                'asal_suku' => 'required',
                'kriteria_values' => 'required|array', // Pastikan kriteria_values ada
                'kriteria_values.*' => 'required', // Validasi setiap item di dalam kriteria_values
            ],
            [
                '*.required' => 'Kolom wajib diisi',
                '*.numeric' => 'Kolom hanya diisi angka',
                'kriteria_values.*.required' => 'Kolom wajib diisi', // Custom error untuk kriteria_values
            ]
        );

        // Ambil data warga dari request
        $wargaData = [
            'nomor_kk' => $request->nomor_kk,
            'nama_kk' => $request->nama_kk,
            'tahun_id' => $request->periode_id,
            'provinsi' => $request->provinsi,
            'kabupaten' => $request->kabupaten,
            'kampung' => $request->kampung,
            'rt' => $request->rt,
            'rw' => $request->rw,
            'pekerjaan' => $request->pekerjaan,
            'agama' => $request->agama,
            'jenis_kelamin' => $request->jenis_kelamin,
            'asal_suku' => $request->asal_suku,
            'status' => 0,
            'created_at' => Carbon::now('Asia/Jayapura')
        ];

        // Menyimpan data warga (tabel warga)
        $warga = ModelsWarga::create($wargaData);

        // Simpan data kriteria_values secara dinamis berdasarkan kolom yang ada pada tabel warga
        foreach ($request->input('kriteria_values', []) as $kriteriaName => $kriteriaValue) {
            // Cek apakah kolom yang ada di kriteria_values ada pada tabel warga
            if (Schema::hasColumn('warga', $kriteriaName)) {
                // Menyimpan nilai kriteria_values ke kolom yang sesuai pada tabel warga
                $warga->{$kriteriaName} = $kriteriaValue;
            }
        }

        // Simpan perubahan pada data warga setelah menambahkan kriteria_values
        $warga->save();

        // Mengarahkan kembali dengan pesan sukses
        return back()->with([
            'notif_status' => 'success',
            'notif_message' => 'Data warga berhasil ditambahkan!',
        ]);
    }
}
