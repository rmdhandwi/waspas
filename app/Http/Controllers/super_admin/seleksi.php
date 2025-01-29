<?php

namespace App\Http\Controllers\super_admin;

use App\Http\Controllers\Controller;
use App\Models\hasil;
use App\Models\Kriteria;
use App\Models\perhitungan;
use App\Models\periode;
use App\Models\SubKriteria;
use App\Models\Warga;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class seleksi extends Controller
{

    // public function hitungWaspas(Request $request)
    // {
    //     // Validasi input periodeId
    //     $request->validate([
    //         'periode_id' => 'required'
    //     ], [
    //         'periode_id.required' => 'Harus memilih periode'
    //     ]);

    //     // Ambil periode yang dipilih
    //     $periodeId = $request->periode_id;

    //     $periode = Periode::find($periodeId);
    //     if (!$periode) {
    //         return back()->with([
    //             'notif_status' => 'error',
    //             'notif_message' => 'Periode tidak ditemukan!',
    //         ]);
    //     }

    //     // Ambil data kriteria dan subkriteria
    //     $kriteria = Kriteria::where('status', 'Aktif')->get(); // Semua kriteria
    //     $subkriteria = Subkriteria::all(); // Semua subkriteria

    //     // Validasi jumlah bobot kriteria
    //     $totalBobot = $kriteria->sum('bobot');
    //     if ($totalBobot < 100) {
    //         return back()->with([
    //             'notif_status' => 'error',
    //             'notif_message' => 'Jumlah total bobot kriteria kurang dari 100!',
    //         ]);
    //     }

    //     $periode = Periode::where('id', $periodeId)
    //         ->select('id', 'tahun')->get();

    //     // Ambil data warga dengan status 0 berdasarkan periode
    //     $warga = Warga::where('tahun_id', $periodeId)
    //         ->where('status', 0) // Hanya warga dengan status 0
    //         ->get();

    //     // Cek jika jumlah warga untuk periode ini kurang dari 1
    //     if ($warga->isEmpty()) {
    //         return back()->with([
    //             'notif_status' => 'error',
    //             'notif_message' => 'Data warga periode ini tidak ditemukan!',
    //         ]);
    //     }

    //     // Validasi apakah ada kolom kosong pada data warga
    //     foreach ($warga as $alt) {
    //         foreach ($kriteria as $criteria) {
    //             $column = $criteria->nama_kriteria;

    //             // Jika ada kolom kriteria yang kosong, kembalikan notifikasi
    //             if (is_null($alt->{$column})) {
    //                 return back()->with([
    //                     'notif_status' => 'error',
    //                     'notif_message' => "Ada data kosong pada warga untuk kriteria $column",
    //                 ]);
    //             }
    //         }
    //     }



    //     // Buat array baru untuk menampung data warga yang sesuai dengan kriteria
    //     $filteredWarga = [];

    //     foreach ($warga as $alt) {
    //         $filteredData = [];

    //         foreach ($kriteria as $criteria) {
    //             $column = $criteria->nama_kriteria;
    //             $filteredData[$column] = $alt->{$column};

    //             // Ambil nilai dari subkriteria yang cocok dengan kolom warga
    //             $value = $alt->{$column};
    //             $matchingSubkriteria = $subkriteria->where('kriteria_id', $criteria->id);
    //             $matchedSubkriteria = $matchingSubkriteria->firstWhere('nama_subkriteria', $value);

    //             // Jika subkriteria ditemukan, ambil nilai subkriteria tersebut
    //             if ($matchedSubkriteria) {
    //                 $filteredData[$column] = $matchedSubkriteria->nilai;
    //             } else {
    //                 // Jika tidak ada kecocokan, tetapkan nilai default (misalnya 0)
    //                 $filteredData[$column] = 0;
    //             }
    //         }

    //         // Simpan data yang telah difilter ke dalam array baru, termasuk ID warga
    //         $filteredWarga[] = [
    //             'id' => $alt->id,
    //             'tahun_id' => $alt->id,
    //             'alternatif' => $alt->nama_kk, // Nama alternatif (misalnya nama KK)
    //             'data' => $filteredData,      // Data yang relevan berdasarkan kriteria
    //             'penghasilan' => $alt->penghasilan, // Ambil data penghasilan
    //             'jumlah_penghuni' => $alt->jumlah_penghuni, // Ambil data jumlah penghuni
    //             'status_kepemilikan_rumah' => $alt->status_kepemilikan_rumah, // Ambil data status kepemilikan rumah
    //         ];
    //     }

    //     // Validasi jika ada nilai kriteria yang 0
    //     foreach ($filteredWarga as $alt) {
    //         foreach ($alt['data'] as $key => $value) {
    //             if ($value == 0) {
    //                 return back()->with([
    //                     'notif_status' => 'error',
    //                     'notif_message' => "Data kolom $key tidak sesuai dengan subkriteria. Mohon periksa kembali data Warga {$alt['alternatif']}.",
    //                 ]);
    //             }
    //         }
    //     }

    //     // Proses normalisasi dan perhitungan Waspas
    //     foreach ($filteredWarga as &$alt) {
    //         $normalizedValues = [];
    //         $altData = $alt['data'];

    //         foreach ($kriteria as $criteria) {
    //             $namaKriteria = $criteria->nama_kriteria;

    //             // Ambil semua nilai terkait kriteria ini dari filteredWarga
    //             $allValues = collect($filteredWarga)->pluck("data.$namaKriteria");

    //             if ($allValues->isNotEmpty() && isset($altData[$namaKriteria])) {
    //                 $subValue = $altData[$namaKriteria];

    //                 // Normalisasi berdasarkan tipe kriteria
    //                 if ($criteria->tipe == 'Cost') {
    //                     $minValue = $allValues->min();
    //                     $normalizedValue = $minValue != 0 ? $minValue / $subValue : 0; // Hindari pembagian dengan nol
    //                 } else { // Benefit
    //                     $maxValue = $allValues->max();
    //                     $normalizedValue = $maxValue != 0 ? $subValue / $maxValue : 0; // Hindari pembagian dengan nol
    //                 }

    //                 // Simpan nilai normalisasi
    //                 $normalizedValues[$criteria->kode_kriteria] = round($normalizedValue, 3);
    //             } else {
    //                 // Jika tidak ada nilai untuk kriteria ini, tetapkan 0
    //                 $normalizedValues[$criteria->kode_kriteria] = 0;
    //             }
    //         }

    //         // Tambahkan hasil normalisasi ke data warga
    //         $alt['normalisasi'] = $normalizedValues;
    //     }

    //     // Menghitung Qi dan melakukan ranking
    //     foreach ($filteredWarga as &$alt) {
    //         $qi = 0; // Bagian additive
    //         $prod = 1; // Bagian multiplicative

    //         foreach ($kriteria as $criteria) {
    //             $kodeKriteria = $criteria->kode_kriteria;
    //             $weight = $criteria->bobot / 100; // Bobot dalam desimal

    //             // Ambil nilai normalisasi
    //             $r_ij = $alt['normalisasi'][$kodeKriteria] ?? 0;

    //             // Bagian additive
    //             $qi += $r_ij * $weight;

    //             // Bagian multiplicative
    //             $prod *= pow($r_ij, $weight);
    //         }

    //         // Kombinasi additive dan multiplicative
    //         $alt['qi'] = round(0.5 * $qi + 0.5 * $prod, 3);
    //     }

    //     // Urutkan hasil berdasarkan Qi
    //     usort($filteredWarga, function ($a, $b) {
    //         return $b['qi'] <=> $a['qi']; // Descending order
    //     });

    //     // Berikan ranking dan status kelayakan berdasarkan Qi
    //     foreach ($filteredWarga as $index => &$alt) {
    //         $alt['ranking'] = $index + 1;

    //         // Tentukan status kelayakan
    //         if ($alt['qi'] < 0.511) {
    //             $alt['status'] = 'Kurang Layak';
    //         } else {
    //             $alt['status'] = 'Layak';
    //         }
    //     }

    //     // Kirim hasil ke tampilan
    //     return Inertia::render('SuperAdmin/Seleksi', [
    //         'result' => $filteredWarga, // Data alternatif dan hasil perhitungan
    //         'periode' => $periode,      // Periode yang dipilih
    //         'kriteria' => $kriteria,    // Kriteria untuk header tabel
    //     ]);
    // }

    // public function hitungWaspas(Request $request)
    // {
    //     // Validasi input periodeId
    //     $request->validate([
    //         'periode_id' => 'required'
    //     ], [
    //         'periode_id.required' => 'Harus memilih periode'
    //     ]);

    //     // Ambil periode yang dipilih
    //     $periodeId = $request->periode_id;

    //     $periode = Periode::find($periodeId);
    //     if (!$periode) {
    //         return back()->with([
    //             'notif_status' => 'error',
    //             'notif_message' => 'Periode tidak ditemukan!',
    //         ]);
    //     }

    //     // Ambil data kriteria dan subkriteria
    //     $kriteria = Kriteria::where('status', 'Aktif')->get();
    //     $subkriteria = Subkriteria::all();

    //     // Validasi jumlah bobot kriteria
    //     $totalBobot = $kriteria->sum('bobot');
    //     if ($totalBobot < 100) {
    //         return back()->with([
    //             'notif_status' => 'error',
    //             'notif_message' => 'Jumlah total bobot kriteria kurang dari 100!',
    //         ]);
    //     }

    //     // Ambil data warga dengan status 0 berdasarkan periode
    //     $warga = Warga::where('tahun_id', $periodeId)
    //         ->where('status', 0)
    //         ->get();

    //     if ($warga->isEmpty()) {
    //         return back()->with([
    //             'notif_status' => 'error',
    //             'notif_message' => 'Data warga periode ini tidak ditemukan!',
    //         ]);
    //     }

    //     foreach ($warga as $alt) {
    //         foreach ($kriteria as $criteria) {
    //             $column = $criteria->nama_kriteria;
    //             if (is_null($alt->{$column})) {
    //                 return back()->with([
    //                     'notif_status' => 'error',
    //                     'notif_message' => "Ada data kosong pada warga untuk kriteria $column",
    //                 ]);
    //             }
    //         }
    //     }

    //     $filteredWarga = [];

    //     foreach ($warga as $alt) {
    //         $filteredData = [];

    //         foreach ($kriteria as $criteria) {
    //             $column = $criteria->nama_kriteria;
    //             $value = $alt->{$column};
    //             $matchingSubkriteria = $subkriteria->where('kriteria_id', $criteria->id);
    //             $matchedSubkriteria = $matchingSubkriteria->firstWhere('nama_subkriteria', $value);

    //             $filteredData[$column] = $matchedSubkriteria ? $matchedSubkriteria->nilai : 0;
    //         }

    //         $filteredWarga[] = [
    //             'id' => $alt->id,
    //             'alternatif' => $alt->nama_kk,
    //             'data' => $filteredData,
    //         ];
    //     }

    //     foreach ($filteredWarga as $alt) {
    //         foreach ($alt['data'] as $key => $value) {
    //             if ($value == 0) {
    //                 return back()->with([
    //                     'notif_status' => 'error',
    //                     'notif_message' => "Data kolom $key tidak sesuai dengan subkriteria. Mohon periksa kembali data Warga {$alt['alternatif']}.",
    //                 ]);
    //             }
    //         }
    //     }

    //     foreach ($filteredWarga as &$alt) {
    //         $normalizedValues = [];

    //         foreach ($kriteria as $criteria) {
    //             $namaKriteria = $criteria->nama_kriteria;
    //             $allValues = collect($filteredWarga)->pluck("data.$namaKriteria");

    //             if ($allValues->isNotEmpty() && isset($alt['data'][$namaKriteria])) {
    //                 $subValue = $alt['data'][$namaKriteria];
    //                 if ($criteria->tipe == 'Cost') {
    //                     $minValue = $allValues->min();
    //                     $normalizedValue = $minValue != 0 ? $minValue / $subValue : 0;
    //                 } else {
    //                     $maxValue = $allValues->max();
    //                     $normalizedValue = $maxValue != 0 ? $subValue / $maxValue : 0;
    //                 }

    //                 $normalizedValues[$criteria->kode_kriteria] = round($normalizedValue, 3);
    //             } else {
    //                 $normalizedValues[$criteria->kode_kriteria] = 0;
    //             }
    //         }

    //         $alt['normalisasi'] = $normalizedValues;
    //     }

    //     foreach ($filteredWarga as &$alt) {
    //         $qi = 0;
    //         $prod = 1;

    //         foreach ($kriteria as $criteria) {
    //             $kodeKriteria = $criteria->kode_kriteria;
    //             $weight = $criteria->bobot / 100;
    //             $r_ij = $alt['normalisasi'][$kodeKriteria] ?? 0;

    //             $qi += $r_ij * $weight;
    //             $prod *= pow($r_ij, $weight);
    //         }

    //         $alt['qi'] = round(0.5 * $qi + 0.5 * $prod, 3);
    //     }

    //     usort($filteredWarga, function ($a, $b) {
    //         return $b['qi'] <=> $a['qi'];
    //     });

    //     // dd($filteredData);

    //     foreach ($filteredWarga as $index => &$alt) {
    //         $alt['ranking'] = $index + 1;

    //         // Pastikan 'data' memiliki kunci 'penghasilan_perbulan' dan 'status_legalitas_lahan'
    //         $penghasilanPerbulan = $alt['data']['penghasilan_perbulan'] ?? null;
    //         $statusLegalitasLahan = $alt['data']['status_legalitas_lahan'] ?? null;

    //         // Tentukan status berdasarkan kondisi
    //         if (
    //             $penghasilanPerbulan == 1 &&
    //             $statusLegalitasLahan == 1
    //         ) {
    //             $alt['status'] = 'Tidak Layak';
    //         } elseif ($alt['qi'] >= 0.511) {
    //             $alt['status'] = 'Layak';
    //             $alt['keterangan'] = 'Penghasilan > 3.000.000 dan status legalitas lahannya tidak diketahui';
    //         } else {
    //             $alt['status'] = 'Tidak Layak';
    //         }
    //     }

    //     return Inertia::render('SuperAdmin/Seleksi', [
    //         'result' => $filteredWarga,
    //         'periode' => $periode,
    //         'kriteria' => $kriteria,
    //     ]);
    // }

    public function hitungWaspas(Request $request)
    {
        // Validasi input periodeId
        $request->validate([
            'periode_id' => 'required'
        ], [
            'periode_id.required' => 'Harus memilih periode'
        ]);

        // Ambil periode yang dipilih
        $periodeId = $request->periode_id;

        $periode = Periode::find($periodeId);
        if (!$periode) {
            return back()->with([
                'notif_status' => 'error',
                'notif_message' => 'Periode tidak ditemukan!',
            ]);
        }

        // Ambil data kriteria dan subkriteria
        $kriteria = Kriteria::where('status', 'Aktif')->get();
        $subkriteria = Subkriteria::all();

        // Validasi jumlah bobot kriteria
        $totalBobot = $kriteria->sum('bobot');
        if ($totalBobot < 100) {
            return back()->with([
                'notif_status' => 'error',
                'notif_message' => 'Jumlah total bobot kriteria kurang dari 100!',
            ]);
        }

        // Ambil data warga dengan status 0 berdasarkan periode
        $warga = Warga::where('tahun_id', $periodeId)
            ->where('status', 0)
            ->get();

        if ($warga->isEmpty()) {
            return back()->with([
                'notif_status' => 'error',
                'notif_message' => 'Data warga periode ini tidak ditemukan!',
            ]);
        }

        foreach ($warga as $alt) {
            foreach ($kriteria as $criteria) {
                $column = $criteria->nama_kriteria;
                if (is_null($alt->{$column})) {
                    return back()->with([
                        'notif_status' => 'error',
                        'notif_message' => "Ada data kosong pada warga untuk kriteria $column",
                    ]);
                }
            }
        }

        $filteredWarga = [];

        foreach ($warga as $alt) {
            $filteredData = [];

            foreach ($kriteria as $criteria) {
                $column = $criteria->nama_kriteria;
                $value = $alt->{$column};
                $matchingSubkriteria = $subkriteria->where('kriteria_id', $criteria->id);
                $matchedSubkriteria = $matchingSubkriteria->firstWhere('nama_subkriteria', $value);

                $filteredData[$column] = $matchedSubkriteria ? $matchedSubkriteria->nilai : 0;
            }

            $filteredWarga[] = [
                'id' => $alt->id,
                'alternatif' => $alt->nama_kk,
                'tahun_id' => $alt->tahun_id,
                'data' => $filteredData,
            ];
        }

        foreach ($filteredWarga as $alt) {
            foreach ($alt['data'] as $key => $value) {
                if ($value == 0) {
                    return back()->with([
                        'notif_status' => 'error',
                        'notif_message' => "Data kolom $key tidak sesuai dengan subkriteria. Mohon periksa kembali data Warga {$alt['alternatif']}.",
                    ]);
                }
            }
        }

        foreach ($filteredWarga as &$alt) {
            $normalizedValues = [];

            foreach ($kriteria as $criteria) {
                $namaKriteria = $criteria->nama_kriteria;
                $allValues = collect($filteredWarga)->pluck("data.$namaKriteria");

                if ($allValues->isNotEmpty() && isset($alt['data'][$namaKriteria])) {
                    $subValue = $alt['data'][$namaKriteria];
                    if ($criteria->tipe == 'Cost') {
                        $minValue = $allValues->min();
                        $normalizedValue = $minValue != 0 ? $minValue / $subValue : 0;
                    } else {
                        $maxValue = $allValues->max();
                        $normalizedValue = $maxValue != 0 ? $subValue / $maxValue : 0;
                    }

                    $normalizedValues[$criteria->kode_kriteria] = round($normalizedValue, 3);
                } else {
                    $normalizedValues[$criteria->kode_kriteria] = 0;
                }
            }

            $alt['normalisasi'] = $normalizedValues;
        }

        foreach ($filteredWarga as &$alt) {
            $qi = 0;
            $prod = 1;

            foreach ($kriteria as $criteria) {
                $kodeKriteria = $criteria->kode_kriteria;
                $weight = $criteria->bobot / 100;
                $r_ij = $alt['normalisasi'][$kodeKriteria] ?? 0;

                $qi += $r_ij * $weight;
                $prod *= pow($r_ij, $weight);
            }

            $alt['qi'] = round(0.5 * $qi + 0.5 * $prod, 3);
        }

        // Sorting berdasarkan nilai QI dan alternatif (jika QI sama, urutkan berdasarkan nama alternatif)
        usort($filteredWarga, function ($a, $b) {
            if ($b['qi'] === $a['qi']) {
                return strcmp($a['alternatif'], $b['alternatif']); // Urutkan berdasarkan abjad nama alternatif
            }
            return $b['qi'] <=> $a['qi']; // Urutkan berdasarkan QI secara descending
        });

        // Penanganan jika nilai QI sama
        foreach ($filteredWarga as $index => &$alt) {
            foreach ($filteredWarga as $compAlt) {
                if (
                    $alt['qi'] >= 0.511 && $alt['qi'] === $compAlt['qi'] && $alt['id'] !== $compAlt['id']
                ) {
                    $keterangan = [];

                    // Loop untuk setiap kriteria untuk mengambil nilai yang sesuai
                    foreach ($kriteria as $criteria) {
                        $namaKriteria = $criteria->nama_kriteria;

                        // Ambil nilai langsung dari model Warga berdasarkan nama kriteria
                        $altValue = Warga::find($alt['id'])->{$namaKriteria} ?? null;

                        // Simpan keterangan dengan format "Nama Kriteria: Nilai"
                        $keterangan[] = "$namaKriteria: $altValue";
                    }

                    // Jika ada nilai yang diambil, tampilkan
                    if (!empty($keterangan)) {
                        // Gabungkan keterangan menggunakan koma sebagai pemisah
                        $alt['keterangan'] = implode(', ', $keterangan);
                    } else {
                        // Jika tidak ada nilai yang diambil
                        $alt['keterangan'] = 'Tidak ada nilai yang tersedia.';
                    }
                }
            }
        }


        // dd($filteredWarga);

        foreach ($filteredWarga as $index => &$alt) {
            $alt['ranking'] = $index + 1;

            // Pengecekan penghasilan_perbulan dan status_legalitas_lahan
            $penghasilanPerbulan = $alt['data']['penghasilan_perbulan'] ?? null;
            $statusLegalitasLahan = $alt['data']['status_legalitas_lahan'] ?? null;

            if (
                $alt['qi'] >= 0.511 && $penghasilanPerbulan == 1
            ) {
                $alt['status'] = 'Tidak Layak';
                $alt['keterangan'] = 'Penghasilan > 3.000.000';
            } elseif ($alt['qi'] >= 0.511 &&  $statusLegalitasLahan == 1) {
                $alt['status'] = 'Tidak Layak';
                $alt['keterangan'] = 'Status Legalitas Lahan Tidak Ada/Tidak Diketahui';
            } elseif ($alt['qi'] >= 0.511) {
                $alt['status'] = 'Layak';
            } else {
                $alt['status'] = 'Tidak Layak';
            }
        }

        // dd($filteredWarga);

        return Inertia::render('SuperAdmin/Seleksi', [
            'result' => $filteredWarga,
            'periode' => $periode,
            'kriteria' => $kriteria,
        ]);
    }


    public function saveHasilAkhir(Request $request)
    {
        // Ambil data hasil seleksi yang diterima dari frontend
        $results = $request->results;
        $exportCount = (int)$request->export_count; // Ambil jumlah data yang dimasukkan pengguna

        // Pastikan data hasil seleksi ada
        if (empty($results)) {
            return redirect()->route('wargapage')->with([
                'notif_status' => 'error',
                'notif_message' => 'Tidak ada data hasil seleksi yang diterima.',
            ]);
        }

        // Validasi jumlah data yang akan disimpan
        if ($exportCount <= 0 || $exportCount > count($results)) {
            return redirect()->route('wargapage')->with([
                'notif_status' => 'error',
                'notif_message' => 'Jumlah data yang diminta tidak valid.',
            ]);
        }

        // Filter hanya data dengan status "Layak"
        $layakResults = array_filter($results, function ($result) {
            return $result['status'] === 'Layak';
        });

        // Ambil hanya sejumlah data sesuai exportCount
        $selectedResults = array_slice($layakResults, 0, $exportCount);

        // Mulai transaksi database
        DB::beginTransaction();

        try {
            // Update status warga terlebih dahulu dan simpan data dengan status "Layak" ke tabel hasil_akhir
            foreach ($selectedResults as $result) {
                // Update status warga menjadi 1 (sudah diproses)
                Warga::where('id', $result['id'])->update(['status' => 1]);

                // Simpan data ke tabel hasil_akhir
                hasil::create([
                    'warga_id' => $result['id'], // ID warga
                    'skor_akhir' => $result['qi'], // Nilai Qi
                ]);
            }

            // Setelah memperbarui status, simpan semua data ke tabel perhitungan
            foreach ($results as $result) {
                // Ambil status terkini dari tabel warga berdasarkan ID
                $warga = Warga::where('id', $result['id'])->first();
                $terima = $warga ? $warga->status : null;
                $terimaValue = $terima == 1 ? 'Telah Menerima' : 'Belum Menerima';

                // Cek apakah ada data dengan 'warga_id' dan 'tahun_id' yang sama pada tabel perhitungan
                $lastHitung = perhitungan::where('warga_id', $result['id'])
                ->where('tahun_id', $result['tahun_id']) // Filter berdasarkan tahun_id juga
                    ->count(); // Hitung jumlah data yang ada untuk kombinasi 'warga_id' dan 'tahun_id'

                // Jika ada data yang sudah ada, tambahkan 1 pada kolom 'hitung', jika tidak, set menjadi 1
                $hitungValue = $lastHitung + 1;

                // Simpan data ke tabel perhitungan
                perhitungan::create([
                    'warga_id' => $result['id'],
                    'skor_akhir' => $result['qi'],
                    'status' => $result['status'],
                    'terima' => $terimaValue,
                    'tahun_id' => $result['tahun_id'], // Pastikan tahun_id ikut disimpan
                    'hitung' => $hitungValue, // Menggunakan nilai hitung yang sudah dihitung
                ]);
            }


            // Commit transaksi jika semua berhasil
            DB::commit();

            return redirect()->route('wargapage')->with([
                'notif_status' => 'success',
                'notif_message' => 'Data hasil akhir berhasil disimpan, dan semua perhitungan dicatat.',
            ]);
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollBack();

            return redirect()->route('wargapage')->with([
                'notif_status' => 'error',
                'notif_message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage(),
            ]);
        }
    }


    public function hasilpage()
    {
        // Ambil data hasil seleksi dengan relasi warga dan periode
        $dataSeleksi = hasil::with(['warga', 'warga.periode'])->get();

        // Ambil semua data periode untuk dropdown atau referensi lainnya
        $periode = Periode::all();

        // Kembalikan view dengan data hasil dan periode
        return Inertia::render('SuperAdmin/Hasil', [
            'hasil' => $dataSeleksi,
            'periode' => $periode
        ]);
    }

    public function hasilWarga()
    {
        $dataSeleksi = Perhitungan::with(['warga', 'warga.periode'])->get();
        $periode = Periode::all();

        // Kelompokkan data berdasarkan kolom 'hitung'
        $groupedByHitung = $dataSeleksi->groupBy('hitung');

        return Inertia::render('DataWarga/HasilPage', [
            'perhitungan' => $dataSeleksi,
            'periode' => $periode,
            'groupedByHitung' => $groupedByHitung,
        ]);
    }



    public function cetakPage(Request $request)
    {
        // Validasi input
        $request->validate([
            'periode' => 'required|numeric',
            'tgl' => 'required|date',
            'oleh' => 'required|string',
            'setuju' => 'required|string',
        ], [
            '*.required' => 'Kolom wajib diisi.',
            'tgl.date' => 'Format tanggal tidak valid.',
            '*.numeric' => 'Kolom harus berisi angka.',
        ]);

        // Ambil data periode berdasarkan ID
        $periode = Periode::find($request->periode);
        if (!$periode) {
            return redirect()->back()->with([
                'notif_status' => 'error',
                'notif_message' => 'Periode tidak ditemukan.',
            ]);
        }

        // Ambil data warga berdasarkan periode
        $warga = Warga::where('tahun_id', $request->periode)->get();
        if ($warga->isEmpty()) {
            return redirect()->back()->with([
                'notif_status' => 'error',
                'notif_message' => 'Data warga kosong untuk periode yang dipilih.',
            ]);
        }

        // Ambil hasil berdasarkan warga yang ditemukan
        $wargaIds = $warga->pluck('id'); // Ambil ID semua warga
        $dataHasil = Hasil::whereIn('warga_id', $wargaIds)
            ->with(['warga', 'warga.periode'])
            ->get();

        if ($dataHasil->isEmpty()) {
            return redirect()->back()->with([
                'notif_status' => 'error',
                'notif_message' => 'Tidak ditemukan data hasil untuk dicetak.',
            ]);
        }

        // Render halaman cetak dengan data yang ditemukan
        return Inertia::render('Cetak', [
            'tahun' => $periode->tahun,
            'tgl' => $request->tgl,
            'oleh' => $request->oleh,
            'setuju' => $request->setuju,
            'hasil' => $dataHasil,
        ]);
    }
}
