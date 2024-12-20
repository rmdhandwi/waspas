<?php

namespace App\Http\Controllers\super_admin;

use App\Http\Controllers\Controller;
use App\Models\hasil;
use App\Models\Kriteria;
use App\Models\periode;
use App\Models\SubKriteria;
use App\Models\Warga;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class seleksi extends Controller
{

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

        // Ambil data kriteria dan subkriteria
        $kriteria = Kriteria::all(); // Semua kriteria
        $subkriteria = Subkriteria::all(); // Semua subkriteria

        // Validasi jumlah bobot kriteria
        $totalBobot = $kriteria->sum('bobot');
        if ($totalBobot < 100) {
            return back()->with([
                'notif_status' => 'error',
                'notif_message' => 'Jumlah total bobot kriteria kurang dari 100!',
            ]);
        }

        $periode = Periode::where('id', $periodeId)
            ->select('id', 'tahun')->get();

        // Ambil data warga dengan status 0 berdasarkan periode
        $warga = Warga::where('tahun_id', $periodeId)
            ->where('status', 0) // Hanya warga dengan status 0
            ->get();

        // Cek jika jumlah warga untuk periode ini kurang dari 1
        if ($warga->isEmpty()) {
            return back()->with([
                'notif_status' => 'error',
                'notif_message' => 'Data warga periode ini tidak ditemukan!',
            ]);
        }

        // Validasi apakah ada kolom kosong pada data warga
        foreach ($warga as $alt) {
            foreach ($kriteria as $criteria) {
                $column = $criteria->nama_kriteria;

                // Jika ada kolom kriteria yang kosong, kembalikan notifikasi
                if (is_null($alt->{$column})) {
                    return back()->with([
                        'notif_status' => 'error',
                        'notif_message' => "Ada data kosong pada warga untuk kriteria $column",
                    ]);
                }
            }
        }

        // Buat array baru untuk menampung data warga yang sesuai dengan kriteria
        $filteredWarga = [];

        foreach ($warga as $alt) {
            $filteredData = [];

            foreach ($kriteria as $criteria) {
                $column = $criteria->nama_kriteria;
                $filteredData[$column] = $alt->{$column};

                // Ambil nilai dari subkriteria yang cocok dengan kolom warga
                $value = $alt->{$column};
                $matchingSubkriteria = $subkriteria->where('kriteria_id', $criteria->id);
                $matchedSubkriteria = $matchingSubkriteria->firstWhere('nama_subkriteria', $value);

                // Jika subkriteria ditemukan, ambil nilai subkriteria tersebut
                if ($matchedSubkriteria) {
                    $filteredData[$column] = $matchedSubkriteria->nilai;
                } else {
                    // Jika tidak ada kecocokan, tetapkan nilai default (misalnya 0)
                    $filteredData[$column] = 0;
                }
            }

            // Simpan data yang telah difilter ke dalam array baru, termasuk ID warga
            $filteredWarga[] = [
                'id' => $alt->id,
                'alternatif' => $alt->nama_kk, // Nama alternatif (misalnya nama KK)
                'data' => $filteredData,      // Data yang relevan berdasarkan kriteria
                'penghasilan' => $alt->penghasilan, // Ambil data penghasilan
                'jumlah_penghuni' => $alt->jumlah_penghuni, // Ambil data jumlah penghuni
                'status_kepemilikan_rumah' => $alt->status_kepemilikan_rumah, // Ambil data status kepemilikan rumah
            ];
        }

        // Proses normalisasi dan perhitungan Waspas
        foreach ($filteredWarga as &$alt) {
            $normalizedValues = [];
            $altData = $alt['data'];

            foreach ($kriteria as $criteria) {
                $namaKriteria = $criteria->nama_kriteria;

                // Ambil semua nilai terkait kriteria ini dari filteredWarga
                $allValues = collect($filteredWarga)->pluck("data.$namaKriteria");

                if ($allValues->isNotEmpty() && isset($altData[$namaKriteria])) {
                    $subValue = $altData[$namaKriteria];

                    // Normalisasi berdasarkan tipe kriteria
                    if ($criteria->tipe == 'Cost') {
                        $minValue = $allValues->min();
                        $normalizedValue = $minValue != 0 ? $minValue / $subValue : 0; // Hindari pembagian dengan nol
                    } else { // Benefit
                        $maxValue = $allValues->max();
                        $normalizedValue = $maxValue != 0 ? $subValue / $maxValue : 0; // Hindari pembagian dengan nol
                    }

                    // Simpan nilai normalisasi
                    $normalizedValues[$criteria->kode_kriteria] = round($normalizedValue, 3);
                } else {
                    // Jika tidak ada nilai untuk kriteria ini, tetapkan 0
                    $normalizedValues[$criteria->kode_kriteria] = 0;
                }
            }

            // Tambahkan hasil normalisasi ke data warga
            $alt['normalisasi'] = $normalizedValues;
        }

        // Menghitung Qi dan melakukan ranking
        foreach ($filteredWarga as &$alt) {
            $qi = 0; // Bagian additive
            $prod = 1; // Bagian multiplicative

            foreach ($kriteria as $criteria) {
                $kodeKriteria = $criteria->kode_kriteria;
                $weight = $criteria->bobot / 100; // Bobot dalam desimal

                // Ambil nilai normalisasi
                $r_ij = $alt['normalisasi'][$kodeKriteria] ?? 0;

                // Bagian additive
                $qi += $r_ij * $weight;

                // Bagian multiplicative
                $prod *= pow($r_ij, $weight);
            }

            // Kombinasi additive dan multiplicative
            $alt['qi'] = round(0.5 * $qi + 0.5 * $prod, 3);
        }

        // Urutkan hasil berdasarkan Qi (tidak perlu mengurutkan berdasarkan penghasilan atau jumlah penghuni)
        usort($filteredWarga, function ($a, $b) {
            return $b['qi'] <=> $a['qi']; // Descending order
        });

        // Berikan ranking berdasarkan Qi
        foreach ($filteredWarga as $index => &$alt) {
            $alt['ranking'] = $index + 1;
        }

        // Kirim hasil ke tampilan
        return Inertia::render('SuperAdmin/Seleksi', [
            'result' => $filteredWarga, // Data alternatif dan hasil perhitungan
            'periode' => $periode,      // Periode yang dipilih
            'kriteria' => $kriteria,    // Kriteria untuk header tabel
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

        // Ambil hanya sejumlah data sesuai exportCount
        $selectedResults = array_slice($results, 0, $exportCount);

        // Cek jika ada masalah dalam menyimpan data
        $isSaved = true;

        foreach ($selectedResults as $result) {
            // Simpan hasil akhir untuk setiap warga
            $hasil = hasil::create([
                'warga_id' => $result['id'], // ID warga
                'skor_akhir' => $result['qi'], // Nilai Qi
                'peringkat' => $result['ranking'], // Peringkat
                'created_at' => Carbon::now('Asia/Jayapura')
            ]);

            // Pastikan data berhasil disimpan
            if (!$hasil) {
                $isSaved = false;
                break;
            }

            // Update status warga yang terpilih ke 1 (sudah diproses)
            $updateStatus = Warga::where('id', $result['id'])
                ->update(['status' => 1, 'updated_at' => Carbon::now('Asia/Jayapura')]);

            // Cek jika update status gagal
            if (!$updateStatus) {
                $isSaved = false;
                break;
            }
        }

        // Kembalikan notifikasi berdasarkan hasil
        if ($isSaved) {
            return redirect()->route('wargapage')->with([
                'notif_status' => 'success',
                'notif_message' => 'Data hasil perhitungan berhasil disimpan dan status warga telah diperbarui.',
            ]);
        } else {
            return redirect()->route('wargapage')->with([
                'notif_status' => 'error',
                'notif_message' => 'Terjadi kesalahan saat menyimpan data atau memperbarui status.',
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
