<?php

use App\Http\Controllers\admin\adminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\Authentication;
use App\Http\Controllers\super_admin\kriteria;
use App\Http\Controllers\super_admin\periode;
use App\Http\Controllers\super_admin\subKriteria;
use App\Http\Controllers\super_admin\superAdminController;
use App\Http\Controllers\super_admin\user;
use App\Http\Controllers\Warga;

Route::middleware('guest')->group(function () {
    Route::post('login', [Authentication::class, 'store'])->name('loginSubmit');
});

Route::middleware('auth')->group(function () {
    //    super_admin
    Route::get('Dashboard', [superAdminController::class, 'Dashboard'])->name('super_admin.dashboard');
    
    Route::get('Pengguna', [superAdminController::class, 'UsersPage'])->name('super_admin.pengguna');
    Route::post('Pengguna', [user::class, 'tambahPengguna'])->name('super_admin.tambah.pengguna');
    Route::get('Pengguna/view/{id}', [user::class, 'viewPengguna'])->name('super_admin.view.pengguna');
    Route::post('Pengguna/update/{id}', [user::class, 'updatePengguna']);
    Route::post('Pengguna/hapus/{id}', [user::class, 'hapusPengguna']);

    Route::get('Periode', [periode::class, 'periodePage'])->name('periode');
    Route::post('Periode/Add', [periode::class, 'tambah_periode'])->name('AddPeriode');
    Route::put('updatePeriode/{id}', [periode::class, 'update_periode'])->name('EditPeriode');
    Route::delete('deletePeriode/{id}}', [periode::class, 'delete_periode'])->name('DeletePeriode');
    
    Route::get('Kriteria', [kriteria::class, 'kriteria_page'])->name('super_admin.kriteria');
    Route::post('Kriteria', [kriteria::class, 'tambah_kriteria'])->name('super_admin.tambah.kriteria');
    Route::get('Kriteria/view/{id}', [kriteria::class, 'view_kriteria']);
    Route::put('KriteriaUpdate/{id}', [kriteria::class, 'update_kriteria'])->name("UpdateKriteria");
    Route::delete('KriteriaHapus/{id}', [kriteria::class, 'hapus_kriteria'])->name("DeleteKriteria");

    Route::post('SubKriteria', [subKriteria::class, 'tambah_subKriteria'])->name('super_admin.tambah.sub_kriteria');
    Route::put('UpdateSub/{id}', [subKriteria::class, 'update_subkriteria'])->name('UpdateSub');
    Route::delete('DeleteSub/{id}', [subKriteria::class, 'hapus_subkriteria'])->name('DeleteSub');

    Route::get('admin/Dashboard', [adminController::class, 'Dashboard'])->name('admin.dashboard');

    Route::get('Warga', [Warga::class, 'wargaPage'])->name('wargapage');
    Route::post('AddWarga', [Warga::class, 'store'])->name('AddWarga');
    Route::put('UpdateWarga/{id}', [Warga::class, 'updateDataWarga'])->name('UpdateWarga');
    Route::delete('DeleteWarga/{id}', [Warga::class, 'hapusDataWarga'])->name('DeleteWarga');


    Route::get('/logout', [Authentication::class, 'destroy'])->name('logout');
});
