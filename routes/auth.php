<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\Authentication;

use App\Http\Controllers\super_admin\superAdminController;
Route::middleware('guest')->group(function () {
   Route::post('login', [Authentication::class, 'store']);
});

Route::middleware('auth')->group(function () {
    //    super_admin
    Route::get('super_admin/Dashboard', [superAdminController::class, 'Dashboard'])->name('super_admin.dashboard');

    Route::get('/logout',[Authentication::class, 'destroy'])->name('logout');
});
