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
                'pekerjaan' => 'required',
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
        $pekerjaanFormat = strtolower(str_replace(' ', '_', $request->pekerjaan));
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
            'pekerjaan' => $pekerjaanFormat,
            'agama' => $request->agama,
            'jenis_kelamin' => $request->jenis_kelamin,
            'asal_suku' => $asalsukuFormat,
            'status' => 0,
            'created_at' => Carbon::now('Asia/Jayapura'),
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

        $namaKriteriaFormat = strtolower(str_replace(' ', '_', $request->nama_kk));
        $pekerjaanFormat = strtolower(str_replace(' ', '_', $request->pekerjaan));
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
            'pekerjaan' => $pekerjaanFormat,
            'agama' => $request->agama,
            'jenis_kelamin' => $request->jenis_kelamin,
            'asal_suku' => $asalsukuFormat,
            'status' => 0,
            'updated_at' => Carbon::now('Asia/Jayapura'), // Update timestamp
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

    // public function upload(Request $request)
    // {
    //     // Validasi file yang diunggah
    //     $request->validate([
    //         'file' => 'required|mimes:csv,txt'
    //     ], [
    //         'file.required' => 'File harus diunggah.',
    //         'file.mimes' => 'Hanya file CSV yang diperbolehkan.'
    //     ]);

    //     // Proses unggahan file dan penyimpanan data ke database
    //     try {
    //         $file = $request->file('file');
    //         $csvData = array_map('str_getcsv', file($file));

    //         // Lewati baris pertama jika itu adalah header
    //         $header = array_shift($csvData);

    //         // Array untuk menyimpan nomor KK yang sudah diproses
    //         $processedNomorKk = [];

    //         foreach ($csvData as $row) {
    //             $nomorKk = $row[1];
    //             $tahunId = $row[0]; // Tahun dari CSV (row[0])
    //             $status = $row[11]; // Tahun dari CSV (row[11])

    //             // Cek apakah nomor KK, dengan peeriode yang sama dan statusnya tidak 1 (untuk cek duplikasi dalam file)
    //             if (in_array($nomorKk, $processedNomorKk)) {
    //                 return redirect()->back()->with([
    //                     'notif_status' => 'error',
    //                     'notif_message' => 'Nomor KK ' . $nomorKk . ' duplikat dalam file CSV!'
    //                 ]);
    //             }

    //             // Cek apakah nomor KK sudah ada di database
    //             $existingData = ModelsWarga::where('nomor_kk', $nomorKk)->first();

    //             if ($existingData) {
    //                 return redirect()->back()->with([
    //                     'notif_status' => 'error',
    //                     'notif_message' => 'Nomor KK ' . $nomorKk . ' sudah terdaftar! dengan nama: ' . $existingData->nama_kk
    //                 ]);
    //             }

    //             // Cek apakah tahun_id ada di tabel periode
    //             $periode = periode::where('tahun', $tahunId)->first(); // Pastikan `Period` adalah model untuk tabel periode

    //             if (!$periode) {
    //                 return redirect()->back()->with([
    //                     'notif_status' => 'error',
    //                     'notif_message' => 'Tahun ID ' . $tahunId . ' tidak ditemukan di tabel periode!'
    //                 ]);
    //             }

    //             // Gunakan id dari periode yang ditemukan
    //             $periodeId = $periode->id;

    //             // Pemetaan kolom CSV ke kolom database
    //             ModelsWarga::create([
    //                 'tahun_id' => $periodeId, // Gunakan id periode
    //                 'nomor_kk' => $nomorKk,
    //                 'nama_kk' => strtolower(str_replace(' ', '_', $row[2])),
    //                 'provinsi' => strtolower(str_replace(' ', '_', $row[3])),
    //                 'kabupaten' => strtolower(str_replace(' ', '_', $row[4])),
    //                 'kampung' => strtolower(str_replace(' ', '_', $row[5])),
    //                 'rt' => strtolower(str_replace(' ', '_', $row[6])),
    //                 'rw' => strtolower(str_replace(' ', '_', $row[7])),
    //                 'asal_suku' => strtolower(str_replace(' ', '_', $row[8])),
    //                 'pekerjaan' => strtolower(str_replace(' ', '_', $row[9])),
    //                 'agama' => strtolower(str_replace(' ', '_', $row[10])),
    //                 'status' => $status,
    //                 'jenis_kelamin' => strtolower(str_replace(' ', '_', $row[12])),
    //                 'created_at' => now(),  // Mengasumsikan Anda ingin mengatur timestamp saat ini
    //                 'updated_at' => now(),  // Mengasumsikan Anda ingin mengatur timestamp saat ini
    //                 'penghasilan' => strtolower(str_replace(' ', '_', $row[13])),
    //                 'status_kepemilikan_rumah' => strtolower(str_replace(' ', '_', $row[14])),
    //                 'struktur_bangunan_rumah' => strtolower(str_replace(' ', '_', $row[15])),
    //                 'status_kepemilikan_lahan' => strtolower(str_replace(' ', '_', $row[16])),
    //                 'status_legalitas_lahan' => strtolower(str_replace(' ', '_', $row[17])),
    //                 'jumlah_penghuni' => strtolower(str_replace(' ', '_', $row[18])),
    //                 'kepemilikan_sanitasi' => strtolower(str_replace(' ', '_', $row[19])),
    //                 'tempat_pembuangan_tinja' => strtolower(str_replace(' ', '_', $row[20])),
    //             ]);

    //             // Tambahkan nomor KK yang telah diproses ke dalam array
    //             $processedNomorKk[] = $nomorKk;
    //         }

    //         return redirect()->back()->with([
    //             'notif_status' => 'success',
    //             'notif_message' => 'File CSV berhasil diunggah dan data berhasil disimpan!'
    //         ]);
    //     } catch (\Exception $e) {
    //         return redirect()->back()->with([
    //             'notif_status' => 'error',
    //             'notif_message' => 'Terjadi kesalahan saat memproses file: ' . $e->getMessage()
    //         ]);
    //     }
    // }


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
                $tahunId = $row[0];  // Tahun dari CSV
                $nomorKk = $row[1]; // Nomor KK
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
                    'nama_kk' => strtolower(str_replace(' ', '_', $row[2])),
                    'provinsi' => strtolower(str_replace(' ', '_', $row[3])),
                    'kabupaten' => strtolower(str_replace(' ', '_', $row[4])),
                    'kampung' => strtolower(str_replace(' ', '_', $row[5])),
                    'rt' => strtolower(str_replace(' ', '_', $row[6])),
                    'rw' => strtolower(str_replace(' ', '_', $row[7])),
                    'asal_suku' => strtolower(str_replace(' ', '_', $row[8])),
                    'pekerjaan' => strtolower(str_replace(' ', '_', $row[9])),
                    'agama' => strtolower(str_replace(' ', '_', $row[10])),
                    'status' => $status,
                    'jenis_kelamin' => strtolower(str_replace(' ', '_', $row[12])),
                    'created_at' => now(),
                    'updated_at' => now(),
                    'penghasilan' => strtolower(str_replace(' ', '_', $row[13])),
                    'status_kepemilikan_rumah' => strtolower(str_replace(' ', '_', $row[14])),
                    'struktur_bangunan_rumah' => strtolower(str_replace(' ', '_', $row[15])),
                    'status_kepemilikan_lahan' => strtolower(str_replace(' ', '_', $row[16])),
                    'status_legalitas_lahan' => strtolower(str_replace(' ', '_', $row[17])),
                    'jumlah_penghuni' => strtolower(str_replace(' ', '_', $row[18])),
                    'kepemilikan_sanitasi' => strtolower(str_replace(' ', '_', $row[19])),
                    'tempat_pembuangan_tinja' => strtolower(str_replace(' ', '_', $row[20])),
                ]);

                // Tandai kombinasi ini sudah diproses
                $processedData[] = $uniqueKey;
            }

            return redirect()->back()->with([
                'notif_status' => 'success',
                'notif_message' => "File CSV berhasil diunggah.\nJumlah data yang dengan status = 1: {$statusOneCountCsv}.\Sudah Pernah  Menerima: {$conflictCount}."
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'notif_status' => 'error',
                'notif_message' => 'Terjadi kesalahan saat memproses file: ' . $e->getMessage()
            ]);
        }
    }
}
