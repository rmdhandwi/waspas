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
        $dataSubKriteria = SubKriteria::with(['kriteria' => function ($query) {
            $query->select('id', 'nama_kriteria', 'kode_kriteria');
        }])->get();

        return Inertia::render('SuperAdmin/Kriteria/Kriteria', [
            'dataKriteria' => $dataKriteria,
            'dataSubKriteria' => $dataSubKriteria
        ]);
    }

    public function tambah_kriteria(Request $request)
    {
        // Validasi input dari request
        $request->validate([
            'nama' => 'required|string|max:255',
            'nilai_bobot' => 'required|numeric|min:0|max:100',
            'tipe' => 'required',
        ], [
            '*.required' => 'Kolom harus diisi',
            '*.numeric' => 'Hanya angka yang diperbolehkan'
        ]);

        // Menghitung total bobot saat ini dari kriteria yang sudah ada
        $totalBobotSaatIni = ModelsKriteria::sum('bobot') / 100;
        $bobotInput = $request->nilai_bobot / 100;
        $sisaBobot = 1 - $totalBobotSaatIni;

        // Mengecek apakah menambahkan bobot ini akan melebihi 100%
        if ($bobotInput > $sisaBobot) {
            return back()->withErrors([
                'nilai_bobot' => 'Total bobot akan melebihi 100%. Sisa bobot yang dapat diinput adalah: ' . ($sisaBobot * 100) . '%'
            ])->withInput();
        }

        // Mengubah nama kriteria menjadi huruf kecil dan mengganti spasi dengan garis bawah
        $namaKriteriaFormat = strtolower(str_replace(' ', '_', $request->nama));

        // Menyimpan data kriteria baru ke dalam database
        $insert = ModelsKriteria::create([
            'nama_kriteria' => $namaKriteriaFormat,
            'bobot' => $request->nilai_bobot,
            'tipe' => ucfirst($request->tipe),
            'created_at' => Carbon::now('Asia/Jayapura')
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
        // ubah namakriteria
        $newColumnName = strtolower(str_replace(' ', '_', $namaKriteria));

        // cek kolom tabel wwarga dan tambahkan kolom berdasarkan namakriteria
        if (!Schema::hasColumn('warga', $newColumnName)) {
            Schema::table('warga', function (Blueprint $table) use ($newColumnName) {
                $table->string($newColumnName, 100)->nullable();
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
        // Validasi input
        $req->validate(
            [
                'nama' => 'required',
                'nilai_bobot' => 'required|numeric|min:0|max:100',
                'tipe' => 'required',
            ],
            [
                '*.required' => 'Kolom Wajib Diisi',
                '*.numeric' => 'Kolom Wajib Berupa Angka',
            ]
        );

        // Ambil data lama dari database berdasarkan id
        $existingData = ModelsKriteria::find($req->id);

        // Jika data tidak ditemukan, kembalikan dengan pesan error
        if (!$existingData) {
            return redirect()->route('super_admin.kriteria')->with([
                'notif_status' => 'failed',
                'notif_message' => 'Data kriteria tidak ditemukan!',
            ]);
        }

        // Hitung total bobot kecuali kriteria yang sedang diedit
        $currentTotalWeight = ModelsKriteria::where('id', '!=', $req->id)->sum('bobot') / 100;

        // Tambahkan bobot baru ke total
        $newTotalWeight = $currentTotalWeight + ($req->nilai_bobot / 100);

        // Jika total bobot melebihi 1, kembalikan dengan pesan error
        if ($newTotalWeight > 1) {
            return back()->withErrors([
                'nilai_bobot' => 'Total bobot akan melebihi 100%. Bobot saat ini (tanpa data yang sedang diedit) adalah: ' . ($currentTotalWeight * 100) . '%. Sisa bobot yang dapat diinput adalah: ' . ((1 - $currentTotalWeight) * 100) . '%'
            ])->withInput();
        }

        // Cek apakah ada perubahan data
        $isChanged = (
            $existingData->nama_kriteria !== $req->nama ||
            $existingData->bobot != $req->nilai_bobot ||
            strtolower($existingData->tipe) !== strtolower($req->tipe)
        );

        // Jika tidak ada perubahan, kembalikan dengan pesan sukses tanpa update
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

        // Lanjutkan update data kriteria
        $updateData = [
            'nama_kriteria' => $namaKriteriaFormat,
            'bobot' => $req->nilai_bobot,
            'tipe' => ucfirst($req->tipe),
        ];

        // Proses update data
        $update = $existingData->update($updateData);

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
