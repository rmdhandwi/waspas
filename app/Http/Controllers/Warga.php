<?php

namespace App\Http\Controllers;

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
        $dataWarga = ModelsWarga::join('periode', 'warga.tahun_id', '=', 'periode.id')
            ->select('warga.*', 'periode.tahun')
            ->orderBy('warga.nama_kk', 'asc')
            ->get();
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
        $request->validate(
            [
                'nomor_kk' => 'required|numeric',
                'nama_kk' => 'required',
                'periode_id' => 'required',
                'provinsi' => 'required',
                'kabupaten' => 'required',
                'kampung' => 'required',
                'rt' => 'required|numeric',
                'rw' => 'required|numeric',
                'agama' => 'required',
                'jenis_kelamin' => 'required',
                'asal_suku' => 'required',
                'kriteria_values' => 'required|array',
                'kriteria_values.*' => 'required',
            ],
            [
                '*.required' => 'Kolom wajib diisi',
                '*.numeric' => 'Kolom hanya diisi angka',
                'kriteria_values.*.required' => 'Kolom wajib diisi',
            ]
        );

        // Pengecekan apakah data dengan nomor_kk dan tahun_id (periode_id) sudah ada
        $existingWarga = ModelsWarga::where('nomor_kk', $request->nomor_kk)
            ->where('tahun_id', $request->periode_id)
            ->exists();

        if ($existingWarga) {
            return back()->with([
                'notif_status' => 'error',
                'notif_message' => 'Data dengan Nomor KK dan Tahun yang sama sudah ada!',
            ]);
        }

        // Mengubah nama kriteria menjadi huruf kecil dan mengganti spasi dengan garis bawah
        $namaKriteriaFormat = strtolower(str_replace(' ', '_', $request->nama_kk));
        $asalsukuFormat = strtolower(str_replace(' ', '_', $request->asal_suku));

        // Ambil data warga dari request
        $wargaData = [
            'nomor_kk' => $request->nomor_kk,
            'nama_kk' => $namaKriteriaFormat,
            'tahun_id' => $request->periode_id,
            'provinsi' => $request->provinsi,
            'kabupaten' => $request->kabupaten,
            'kampung' => $request->kampung,
            'rt' => $request->rt,
            'rw' => $request->rw,
            'agama' => $request->agama,
            'jenis_kelamin' => $request->jenis_kelamin,
            'asal_suku' => $asalsukuFormat,
            'status' => 0,
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



    public function updateDataWarga(Request $request, $id)
    {
        // Validasi data warga
        $request->validate(
            [
                'nomor_kk' => 'required|numeric|unique:warga,nomor_kk,' . $id . 'id',
                'nama_kk' => 'required',
                'periode_id' => 'required',
                'provinsi' => 'required',
                'kabupaten' => 'required',
                'kampung' => 'required',
                'rt' => 'required|numeric',
                'rw' => 'required|numeric',
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

        $namaKriteriaFormat = strtolower(str_replace(' ', '_', $request->nama_kk));
        $asalsukuFormat = strtolower(str_replace(' ', '_', $request->asal_suku));
        // Ambil data warga dari request
        $warga = ModelsWarga::findOrFail($id); // Temukan warga berdasarkan ID

        $wargaData = [
            'nomor_kk' => $request->nomor_kk,
            'nama_kk' => $namaKriteriaFormat,
            'tahun_id' => $request->periode_id,
            'provinsi' => $request->provinsi,
            'kabupaten' => $request->kabupaten,
            'kampung' => $request->kampung,
            'rt' => $request->rt,
            'rw' => $request->rw,
            'agama' => $request->agama,
            'jenis_kelamin' => $request->jenis_kelamin,
            'asal_suku' => $asalsukuFormat,
            'status' => 0,
        ];

        // Memperbarui data warga (tabel warga)
        $warga->update($wargaData);

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
            'notif_message' => 'Data warga berhasil diperbarui!',
        ]);
    }

    public function deleteWarga($id)
    {
        // Temukan kategori berdasarkan ID
        $warga = ModelsWarga::findOrFail($id);

        // Hapus data warga
        $delete = $warga->delete();

        // Periksa apakah penghapusan berhasil
        if ($delete) {
            return redirect()->back()->with([
                'notif_status' => 'success',
                'notif_message' => 'Warga berhasil dihapus.'
            ]);
        }
    }


    public function upload(Request $request)
    {
        // Validasi file yang diunggah
        $request->validate([
            'file' => 'required|mimes:csv,txt'
        ], [
            'file.required' => 'File harus diunggah.',
            'file.mimes' => 'Hanya file CSV yang diperbolehkan.'
        ]);

        try {
            $file = $request->file('file');
            $csvData = array_map('str_getcsv', file($file));

            // Lewati baris pertama jika itu adalah header
            $header = array_shift($csvData);

            // Inisialisasi penghitung
            $statusOneCountCsv = 0;
            $conflictCount = 0;
            $processedData = [];

            foreach ($csvData as $row) {
                $tahunId = $row[1];  // Tahun ID dari CSV
                $nomorKk = $row[2]; // Nomor KK
                $status = $row[11]; // Status dari CSV

                // Hitung data dengan status = 1 dari file CSV
                if ($status == 1) {
                    $statusOneCountCsv++;
                }

                // Kombinasi yang unik: nomor KK, tahun
                $uniqueKey = $nomorKk . '|' . $tahunId;

                // Cek jika data sudah diproses
                if (in_array($uniqueKey, $processedData)) {
                    continue;
                }

                // Cek di database apakah ada nomor KK yang sama tetapi tahun berbeda dan status = 1
                $conflictData = ModelsWarga::where('nomor_kk', $nomorKk)
                    ->where('tahun_id', '!=', $tahunId)
                    ->where('status', 1)
                    ->exists();

                if ($conflictData) {
                    $conflictCount++;
                    $processedData[] = $uniqueKey; // Tandai sebagai diproses
                    continue; // Lewati proses berikutnya
                }

                // Cek apakah tahun_id ada di tabel periode
                $periode = periode::where('tahun', $tahunId)->first();
                if (!$periode) {
                    return redirect()->back()->with([
                        'notif_status' => 'error',
                        'notif_message' => 'Tahun ID ' . $tahunId . ' tidak ditemukan di tabel periode!'
                    ]);
                }

                // Gunakan id dari periode yang ditemukan
                $periodeId = $periode->id;

                // Simpan data ke database jika memenuhi semua validasi
                ModelsWarga::create([
                    'tahun_id' => $periodeId,
                    'nomor_kk' => $nomorKk,
                    'nama_kk' => $row[3],
                    'provinsi' => $row[4],
                    'kabupaten' => $row[5],
                    'kampung' => $row[6],
                    'rt' => $row[7],
                    'rw' => $row[8],
                    'asal_suku' => $row[9],
                    'agama' => $row[10],
                    'status' => $status,
                    'jenis_kelamin' => $row[12],
                    'penghasilan_perbulan' => $row[13] ?? null,
                    'status_kepemilikan_rumah' => $row[14] ?? null,
                    'struktur_bangunan_rumah' => $row[15] ?? null,
                    'status_kepemilikan_lahan' => $row[16] ?? null,
                    'status_legalitas_lahan' => $row[17] ?? null,
                    'jumlah_penghuni' => $row[18] ?? null,
                    'kepemilikan_sanitasi' => $row[19] ?? null,
                    'tempat_pembuangan_tinja' => $row[20] ?? null,
                    'bangunan_diatas_lahan_sempadan_sungai_atau_kali' => $row[21] ?? null,
                    'bangunan_berada_di_daerah_buangan_limbah_pabrik_atau_sutet' => $row[22] ?? null,
                    'luas_lantai_bangunan_rumah/jiwa' => $row[23] ?? null,
                    'kondisi_atap_terluas' => $row[24] ?? null,
                    'kondisi_dinding_terluas' => $row[25] ?? null,
                    'jenis_lantai_terluas' => $row[26] ?? null,
                    'sumber_air_minum,_mandi,_dan_cuci' => $row[27] ?? null,
                    'jarak_sumber_air_dengan_penampungan_tinja_terdekat' => $row[28] ?? null,
                    'kecukupan_air_minum,_mandi,_dan_cuci_tahunan' => $row[29] ?? null,
                    'jenis_kloset' => $row[30] ?? null,
                    'tempat_pembuangan_sampah' => $row[31] ?? null,
                    'jumlah_pengangkutan_sampah' => $row[32] ?? null,
                    'mata_pencaharian_kepala_keluarga' => $row[33] ?? null
                ]);

                // Tandai kombinasi ini sudah diproses
                $processedData[] = $uniqueKey;
            }

            return redirect()->back()->with([
                'notif_status' => 'success',
                'notif_message' => "File CSV berhasil diunggah.\nJumlah data dengan status = 1: {$statusOneCountCsv}.\nSudah Pernah Menerima: {$conflictCount}."
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'notif_status' => 'error',
                'notif_message' => 'Terjadi kesalahan saat memproses file: ' . $e->getMessage()
            ]);
        }
    }

}
