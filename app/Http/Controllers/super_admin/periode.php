<?php

namespace App\Http\Controllers\super_admin;

use App\Http\Controllers\Controller;
use App\Models\periode as ModelPeriode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class periode extends Controller
{

    public function periodePage()
    {
        $dataPeriode = ModelPeriode::all();

        return Inertia::render('SuperAdmin/Periode', [
            'periode' => $dataPeriode
        ]);
    }

    public function tambah_periode(Request $request)
    {
        $request->validate(
            [
                'tahun' => 'required|unique:periode,tahun'
            ],
            [
                'tahun.required' => 'Kolom harus diisi.',
                'tahun.unique' => 'Tahun sudah ditambahkan.'
            ]
        );


        ModelPeriode::create([
            'tahun' => $request->tahun,
            'created_at' => Carbon::now('Asia/Jayapura)'),
        ]);

        return back()->with([
            'notif_status' => 'success',
            'notif_message' => 'Data periode berhasil ditambahkan!',
        ]);
    }

    public function update_periode(Request $request, $id)
    {
        // Validation for editing period
        $request->validate(
            [
                'tahun' => 'required|unique:periode,tahun,' . $id . ',id'
            ],
            [
                'tahun.required' => 'Kolom harus diisi.',
                'tahun.unique' => 'Tahun sudah ditambahkan.'
            ]
        );

        // Update the period record
        $periode = ModelPeriode::findOrFail($id);
        $periode->update([
            'tahun' => $request->tahun,
            'updated_at' => Carbon::now('Asia/Jayapura'),
        ]);

        return back()->with([
            'notif_status' => 'success',
            'notif_message' => 'Data periode berhasil diperbarui!',
        ]);
    }

    public function delete_periode($id)
    {
        // Temukan kategori berdasarkan ID
        $kategori = ModelPeriode::findOrFail($id);

        // Hapus data kategori
        $delete = $kategori->delete();

        // Periksa apakah penghapusan berhasil
        if ($delete) {
            return redirect()->back()->with([
                'notif_status' => 'success',
                'notif_message' => 'Periode berhasil dihapus.'
            ]);
        }
    }
}
