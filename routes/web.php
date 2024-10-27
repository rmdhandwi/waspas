<?php

use App\Http\Controllers\Auth\Authentication;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/',[Authentication::class, 'loginPage'])->name('login')->middleware('guest');
// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

require __DIR__.'/auth.php';
