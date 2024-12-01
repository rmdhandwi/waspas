<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\dataWarga;
use App\Models\hasil;
use App\Models\warga;
use Illuminate\Http\Request;
use Inertia\Inertia;

class adminController extends Controller
{
    //
    public function Dashboard()
    {
        $countWarga = Warga::count();
        $countHasil = Hasil::count();

        // Hitung jumlah warga berdasarkan periode
        $wargaPerPeriode = Warga::selectRaw('periode.tahun, COUNT(warga.id) as total')
            ->join('periode', 'warga.tahun_id', '=', 'periode.id')
            ->groupBy('periode.tahun')
            ->orderBy('periode.tahun')
            ->get();

        // Hitung warga berdasarkan status
        $wargaStatus = Warga::selectRaw('periode.tahun, status, COUNT(*) as total')
            ->join('periode', 'warga.tahun_id', '=', 'periode.id')
            ->groupBy('periode.tahun', 'status')
            ->get()
            ->groupBy('tahun')
            ->map(function ($group) {
                return [
                    'received' => $group->where('status', 1)->sum('total'),
                    'notReceived' => $group->where('status', 0)->sum('total'),
                ];
            });

        return Inertia::render('SuperAdmin/Dashboard', [
            'countWarga' => $countWarga,
            'countHasil' => $countHasil,
            'wargaPerPeriode' => $wargaPerPeriode,
            'wargaStatus' => $wargaStatus,
        ]);
    }
}
