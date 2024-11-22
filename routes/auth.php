<?php

use App\Http\Controllers\admin\adminController;
use App\Http\Controllers\Admin\DataWarga;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\Authentication;
use App\Http\Controllers\super_admin\kriteria;
use App\Http\Controllers\super_admin\subKriteria;
use App\Http\Controllers\super_admin\superAdminController;
use App\Http\Controllers\super_admin\user;

Route::middleware('guest')->group(function () {
   Route::post('login', [Authentication::class, 'store']);
});

Route::middleware('auth')->group(function () {
    //    super_admin
    Route::get('super_admin/Dashboard', [superAdminController::class, 'Dashboard'])->name('super_admin.dashboard');

    Route::get('super_admin/Pengguna', [superAdminController::class, 'UsersPage'])->name('super_admin.pengguna');
    Route::post('super_admin/Pengguna', [user::class, 'tambahPengguna'])->name('super_admin.tambah.pengguna');
    Route::get('super_admin/Pengguna/view/{id}', [user::class, 'viewPengguna'])->name('super_admin.view.pengguna');
    Route::post('super_admin/Pengguna/update/{id}', [user::class, 'updatePengguna']);
    Route::post('super_admin/Pengguna/hapus/{id}', [user::class, 'hapusPengguna']);
    
    Route::get('super_admin/Kriteria', [kriteria::class, 'kriteria_page'])->name('super_admin.kriteria');
    Route::post('super_admin/Kriteria', [kriteria::class, 'tambah_kriteria'])->name('super_admin.tambah.kriteria');
    Route::get('super_admin/Kriteria/view/{id}', [kriteria::class, 'view_kriteria']);
    Route::post('super_admin/Kriteria/update/{id}', [kriteria::class, 'update_kriteria']);
    Route::post('super_admin/Kriteria/hapus/{id}', [kriteria::class, 'hapus_kriteria']);
    
    Route::post('super_admin/SubKriteria', [subKriteria::class, 'tambah_subKriteria'])->name('super_admin.tambah.sub_kriteria');
    Route::post('super_admin/SubKriteria/update/{id}', [subKriteria::class, 'update_subkriteria']);
    Route::post('super_admin/SubKriteria/hapus/{id}', [subKriteria::class, 'hapus_subkriteria']);

    Route::get('admin/Dashboard', [adminController::class, 'Dashboard'])->name('admin.dashboard');
    Route::get('admin/Warga', [DataWarga::class, 'dataWarga'])->name('admin.data_warga');
    Route::post('admin/Warga', [DataWarga::class, 'simpanDataWarga'])->name('admin.tambah.data_warga');
    Route::post('admin/Warga/update/{id}', [DataWarga::class, 'updateDataWarga']);
    Route::post('admin/Warga/hapus/{id}', [DataWarga::class, 'hapusDataWarga']);

    Route::get('/logout',[Authentication::class, 'destroy'])->name('logout');
});
