<?php

namespace App\Http\Controllers\super_admin;

use App\Http\Controllers\Controller;
use App\Models\Kriteria as ModelsKriteria;
use App\Models\seleksi;
use App\Models\SubKriteria;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class kriteria extends Controller
{
    //
    public function kriteria_page()
    {
        $dataKriteria = ModelsKriteria::all();
        $dataKriteriaAktif = ModelsKriteria::where('status', 'Aktif')->get();
        $dataKriteriaTidakAktif = ModelsKriteria::where('status', 'Tidak')->get();
        $dataSubKriteria = SubKriteria::orderBy('kode_sub', 'ASC')
            ->with(['kriteria' => function ($query) {
                $query->select('id', 'nama_kriteria', 'kode_kriteria');
            }])->get();

        return Inertia::render('SuperAdmin/Kriteria/Kriteria', [
            'dataKriteria' => $dataKriteria,
            'dataSubKriteria' => $dataSubKriteria,
            'Aktif' => $dataKriteriaAktif,
            'Taktif' => $dataKriteriaTidakAktif
        ]);
    }

    public function tambah_kriteria(Request $request)
    {
        // Validasi awal untuk status
        $request->validate([
            'status' => 'required|in:Aktif,Tidak',
            'nama' => 'required|string|max:255',
        ], [
            '*.required' => 'Kolom harus diisi',
            'status.in' => 'Hanya boleh terisi Aktif atau Tidak'
        ]);

        // Tambahkan aturan validasi lain jika status adalah "Aktif"
        if ($request->status === 'Aktif') {
            $request->validate([
                'nilai_bobot' => 'required|numeric|min:0|max:100',
                'tipe' => 'required|in:Benefit,Cost',
            ], [
                'nilai_bobot.required' => 'Bobot harus diisi untuk kriteria aktif',
                'nilai_bobot.numeric' => 'Bobot harus berupa angka',
                'tipe.required' => 'Tipe harus diisi untuk kriteria aktif',
                'tipe.in' => 'Hanya boleh terisi Benefit atau Cost',
            ]);

            // Menghitung total bobot saat ini
            $totalBobotSaatIni = ModelsKriteria::where('status', 'Aktif')->sum('bobot') / 100;
            $newWeight = $request->nilai_bobot / 100; // Convert to a fraction
            $newTotalWeight = $totalBobotSaatIni + $newWeight;


            // Jika total bobot melebihi 1, kembalikan dengan pesan error
            if ($newTotalWeight > 1) {
                $remainingWeight = (1 - $totalBobotSaatIni) * 100;

                // Calculate remaining weight
                return back()->withErrors([
                    'nilai_bobot' => 'Total bobot akan melebihi 100%. Sisa bobot yang dapat diinput adalah: ' . $remainingWeight . '%'
                ])->withInput();
            }
        }

        // Mengubah nama kriteria menjadi huruf kecil dan mengganti spasi dengan garis bawah
        $namaKriteriaFormat = strtolower(str_replace(' ', '_', $request->nama));

        // Menyimpan data kriteria baru ke dalam database
        $insert = ModelsKriteria::create([
            'nama_kriteria' => $namaKriteriaFormat,
            'bobot' => $request->status === 'Aktif' ? $request->nilai_bobot : null, // Set bobot ke null jika status Tidak
            'tipe' => $request->status === 'Aktif' ? $request->tipe : null,        // Set tipe ke null jika status Tidak
            'status' => $request->status,
        ]);

        // Jika penyimpanan berhasil, tambahkan kolom ke tabel 'warga'
        if ($insert) {
            $this->addColumnToWarga($namaKriteriaFormat);
            return back()->with([
                'notif_status' => 'success',
                'notif_message' => 'Data kriteria berhasil ditambahkan!',
                'is_kriteria' => true,
            ]);
        }
    }

    private function addColumnToWarga($namaKriteria)
    {
        // ubah nama kriteria
        $newColumnName = strtolower(str_replace(' ', '_', $namaKriteria));

        // cek kolom tabel 'warga' dan tambahkan kolom berdasarkan nama kriteria
        if (!Schema::hasColumn('warga', $newColumnName)) {
            Schema::table('warga', function (Blueprint $table) use ($newColumnName) {
                $table->text($newColumnName)->nullable();
            });
        }
    }

    public function view_kriteria(Request $req)
    {
        // Validasi ID yang diterima
        $req->validate([
            'id' => 'required|integer|exists:kriteria,id',
        ]);

        // Ambil data kriteria berdasarkan ID
        $dataKriteria = ModelsKriteria::find($req->id);

        // Jika data kriteria tidak ditemukan, tampilkan halaman 404
        if (!$dataKriteria) {
            abort(404, 'Kriteria tidak ditemukan');
        }

        // Kembalikan data ke view menggunakan Inertia
        return Inertia::render('SuperAdmin/Kriteria/View', [
            'dataKriteria' => $dataKriteria,
        ]);
    }


    public function update_kriteria(Request $req)
    {
        // Validasi awal untuk status dan nama
        $req->validate(
            [
                'nama' => 'required|string|max:255',
                'status' => 'required|in:Aktif,Tidak',
            ],
            [
                '*.required' => 'Kolom wajib diisi',
                'status.in' => 'Hanya boleh terisi Aktif atau Tidak',
            ]
        );

        // Validasi tambahan jika status adalah "Aktif"
        if ($req->status === 'Aktif') {
            $req->validate(
                [
                    'nilai_bobot' => 'required|numeric|min:0|max:100',
                    'tipe' => 'required|in:Benefit,Cost',
                ],
                [
                    'nilai_bobot.required' => 'Bobot harus diisi untuk kriteria aktif',
                    'nilai_bobot.numeric' => 'Bobot harus berupa angka',
                    'nilai_bobot.min' => 'Bobot tidak boleh kurang dari 0%',
                    'nilai_bobot.max' => 'Bobot tidak boleh lebih dari 100%',
                    'tipe.required' => 'Tipe harus diisi untuk kriteria aktif',
                    'tipe.in' => 'Hanya boleh terisi Benefit atau Cost',
    
                ]
            );

            // Hitung total bobot kecuali kriteria yang sedang diedit
            $currentTotalWeight = ModelsKriteria::where('status', 'Aktif')
                ->where('id', '!=', $req->id)
                ->sum('bobot') / 100;

            // Tambahkan bobot baru ke total
            $newWeight = $req->nilai_bobot / 100; // Convert to a fraction
            $newTotalWeight = $currentTotalWeight + $newWeight;


            // Jika total bobot melebihi 1, kembalikan dengan pesan error
            if ($newTotalWeight > 1) {
                $remainingWeight = (1 - $currentTotalWeight) * 100;

                // Calculate remaining weight
                return back()->withErrors([
                    'nilai_bobot' => 'Total bobot akan melebihi 100%. Sisa bobot yang dapat diinput adalah: ' . $remainingWeight . '%'
                ])->withInput();
            }
        }

        // Ambil data lama dari database berdasarkan id
        $existingData = ModelsKriteria::find($req->id);

        // Jika data tidak ditemukan, kembalikan dengan pesan error
        if (!$existingData) {
            return redirect()->route('super_admin.kriteria')->with([
                'notif_status' => 'failed',
                'notif_message' => 'Data kriteria tidak ditemukan!',
            ]);
        }

        // Cek apakah ada perubahan data
        $isChanged = (
            $existingData->nama_kriteria !== $req->nama ||
            $existingData->bobot != ($req->status === 'Aktif' ? $req->nilai_bobot : null) ||
            $existingData->tipe != ($req->status === 'Aktif' ? $req->tipe : null) ||
            $existingData->status !== $req->status ||
            $existingData->keterangan !== ($req->status === 'Aktif' ? $req->keterangan : "Tidak")
        );

        // Jika tidak ada perubahan, kembalikan dengan pesan info
        if (!$isChanged) {
            return redirect()->route('super_admin.kriteria')->with([
                'notif_status' => 'info',
                'notif_message' => 'Tidak ada perubahan yang perlu diperbarui!',
            ]);
        }

        // Jika ada perubahan pada nama kriteria, update nama kolom di tabel 'warga'
        if ($existingData->nama_kriteria !== $req->nama) {
            $oldColumnName = strtolower(str_replace(' ', '_', $existingData->nama_kriteria));
            $newColumnName = strtolower(str_replace(' ', '_', $req->nama));

            // Jika kolom dengan nama baru belum ada, lakukan rename
            if (Schema::hasColumn('warga', $oldColumnName)) {
                Schema::table('warga', function (Blueprint $table) use ($oldColumnName, $newColumnName) {
                    $table->renameColumn($oldColumnName, $newColumnName);
                });
            }
        }

        // Mengubah nama kriteria menjadi huruf kecil dan mengganti spasi dengan garis bawah
        $namaKriteriaFormat = strtolower(str_replace(' ', '_', $req->nama));

        // dd($req->all());
        // Persiapkan data untuk update
        $updateData = [
            'nama_kriteria' => $namaKriteriaFormat,
            'bobot' => $req->status === 'Aktif' ? $req->nilai_bobot : null,
            'tipe' => $req->status === 'Aktif' ? $req->tipe : null,
            'status' => $req->status,
        ];

        // Lakukan update data
        $update = $existingData->update($updateData);

        // Berikan notifikasi berdasarkan hasil update
        if ($update) {
            return redirect()->route('super_admin.kriteria')->with([
                'notif_status' => 'success',
                'notif_message' => 'Berhasil update kriteria!',
            ]);
        } else {
            return redirect()->route('super_admin.kriteria')->with([
                'notif_status' => 'failed',
                'notif_message' => 'Gagal update kriteria :(',
            ]);
        }
    }

    public function hapus_kriteria(Request $req)
    {
        // Temukan kriteria berdasarkan ID yang diberikan
        $kriteria = ModelsKriteria::find($req->id);

        if (!$kriteria) {
            // Mengembalikan pesan kesalahan jika kriteria tidak ditemukan
            return redirect()->route('super_admin.kriteria')->with([
                'notif_status' => 'failed',
                'notif_message' => 'Kriteria tidak ditemukan :(',
            ]);
        }

        // Ambil nama kriteria yang akan dihapus dan format sesuai aturan (huruf kecil dan spasi jadi garis bawah)
        $namaKriteria = strtolower(str_replace(' ', '_', $kriteria->nama_kriteria));

        // Hapus kriteria
        $kriteria->delete();

        // Periksa apakah kolom dengan nama kriteria ada di tabel 'seleksi'
        if (Schema::hasColumn('warga', $namaKriteria)) {
            // Hapus kolom jika ada
            Schema::table('warga', function (Blueprint $table) use ($namaKriteria) {
                $table->dropColumn($namaKriteria);
            });
        }

        // Mengembalikan pesan sukses
        return redirect()->route('super_admin.kriteria')->with([
            'notif_status' => 'success',
            'notif_message' => 'Berhasil menghapus kriteria!',
        ]);
    }
}
