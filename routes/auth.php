<?php

use App\Http\Controllers\admin\adminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\Authentication;
use App\Http\Controllers\super_admin\kriteria;
use App\Http\Controllers\super_admin\periode;
use App\Http\Controllers\super_admin\seleksi;
use App\Http\Controllers\super_admin\subKriteria;
use App\Http\Controllers\super_admin\superAdminController;
use App\Http\Controllers\super_admin\user;
use App\Http\Controllers\Warga;

Route::middleware('guest')->group(function () {
    Route::post('login', [Authentication::class, 'store'])->name('loginSubmit');

    Route::post('register', [user::class, 'register'])->name('registerSubmit');

    Route::get('Hasil Seleksi Warga', [seleksi::class, 'hasilWarga'])->name('hasilWarga');
});

Route::middleware('auth')->group(function(){
    Route::get('Dashboard', [superAdminController::class, 'Dashboard'])->name('dashboard');

    Route::get('/logout', [Authentication::class, 'destroy'])->name('logout');
    Route::get('Warga', [Warga::class, 'wargaPage'])->name('wargapage');
    Route::get('Hasil Seleksi', [seleksi::class, 'hasilpage'])->name('hasil');
    Route::post('Cetak Hasil', [seleksi::class, 'cetakPage'])->name('cetakPage');
});

Route::middleware(['auth', 'kepala'])->group(function(){
   
    Route::post('Seleksi', [seleksi::class, 'hitungWaspas'])->name('seleksi');
    // Route::post('Tes', [seleksi::class, 'hitungWaspas'])->name('seleksi');
    Route::post('Hasil', [seleksi::class, 'saveHasilAkhir'])->name('simpanDataHasil');
});

Route::middleware(['auth','perangkat'])->group(function () {
    // User
    Route::get('Pengguna', [user::class, 'userpage'])->name('super_admin.pengguna');
    Route::post('Pengguna', [user::class, 'tambahPengguna'])->name('tambahUser');
    Route::put('UpdatePengguna/{id}', [user::class, 'updateUser'])->name('updateUser');
    Route::delete('DeletePengguna/{id}', [user::class, 'hapusPengguna'])->name('deleteUser');

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

    Route::post('AddWarga', [Warga::class, 'store'])->name('AddWarga');
    Route::put('UpdateWarga/{id}', [Warga::class, 'updateDataWarga'])->name('UpdateWarga');
    Route::delete('DeleteWarga/{id}', [Warga::class, 'deleteWarga'])->name('DeleteWarga');
    
    Route::post('UpluadWarga', [Warga::class, 'upload'])->name('uploadCSV');
});
