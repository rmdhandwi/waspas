<?php

namespace App\Http\Controllers\super_admin;

use App\Http\Controllers\Controller;
use App\Models\hasil;
use App\Models\Kriteria;
use App\Models\periode;
use App\Models\SubKriteria;
use App\Models\User;
use App\Models\warga;
use Inertia\Inertia;

class superAdminController extends Controller
{
    //
    public function Dashboard()
    {
        $countKriteria = Kriteria::count();
        $countUsers = User::count();
        $countSubkriteria = SubKriteria::count();
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
            'countKriteria' => $countKriteria,
            'countUsers' => $countUsers,
            'countSub' => $countSubkriteria,
            'countWarga' => $countWarga,
            'countHasil' => $countHasil,
            'wargaPerPeriode' => $wargaPerPeriode,
            'wargaStatus' => $wargaStatus,
        ]);
    }

}
