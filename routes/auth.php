<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\Authentication;
use App\Http\Controllers\super_admin\kriteria;
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

    Route::get('/logout',[Authentication::class, 'destroy'])->name('logout');
});
