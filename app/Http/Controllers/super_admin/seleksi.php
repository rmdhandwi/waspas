<?php

namespace App\Http\Controllers\super_admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\dataWarga as ModelWarga;
use App\Models\Kriteria;
use App\Models\seleksi as ModelsSeleksi;
use App\Models\SubKriteria;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class seleksi extends Controller
{

    public function seleksipage()
    {
        // Ambil semua data warga dari database
        $warga = ModelWarga::all();
        $subkriteria = SubKriteria::with(['kriteria' => function ($query) {
            $query->select('id', 'nama', 'jenis');
        }])->get();
        $seleksi = ModelsSeleksi::with(['warga' => function ($query) {
            $query->select('id', 'nama_kk', 'nomor_kk');
        }])->get();;

        $columns = Schema::getColumnListing('seleksi');
        $columns = array_slice($columns, 3);

        // Kirim data kategori ke tampilan menggunakan Inertia
        return Inertia::render('SuperAdmin/Seleksi', [
            'warga' => $warga,
            'subkriteria' => $subkriteria,
            'seleksi' => $seleksi,
            'column'  => $columns
        ]);
    }

    public function tambahSeleksi(Request $request)
    {
        // Validate the request data
        $request->validate([
            'warga' => 'required',
            'C1' => 'required',
            'C2' => 'required',
            'C3' => 'required',
            'C4' => 'required',
            'C5' => 'required',
            'C6' => 'required',
            'C7' => 'required',
            'C8' => 'required',
        ], [
            '*.required' => 'Kolom harus diisi',
        ]);

        try {
            // Check if the selected warga already has a Seleksi record
            $existingSeleksi = ModelsSeleksi::where('id_warga', $request->input('warga'))->first();

            if ($existingSeleksi) {
                // If a record with the same id_warga already exists, return an error
                return back()->with([
                    'notif_status' => 'error',
                    'notif_message' => 'Data dengan Warga ID yang dipilih sudah ada!',
                ]);
            }

            // Check if the selected warga exists
            $warga = ModelWarga::where('id', $request->input('warga'))->firstOrFail();

            // Create a new Seleksi record
            ModelsSeleksi::create([
                'id_warga' => $warga->id, // Assuming you have an id_warga field in the seleksi table
                'C1' => $request->C1,
                'C2' => $request->C2,
                'C3' => $request->C3,
                'C4' => $request->C4,
                'C5' => $request->C5,
                'C6' => $request->C6,
                'C7' => $request->C7,
                'C8' => $request->C8,
                'created_at' => Carbon::now('Asia/Jayapura')
            ]);

            // Redirect back with success message
            return back()->with([
                'notif_status' => 'success',
                'notif_message' => 'Data seleksi berhasil ditambahkan!',
            ]);
        } catch (\Exception $e) {
            // Handle any errors that may occur
            return back()->with([
                'notif_status' => 'error',
                'notif_message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage(),
            ]);
        }
    }
}
