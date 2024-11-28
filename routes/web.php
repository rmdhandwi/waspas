<?php

use App\Http\Controllers\Auth\Authentication;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/',[Authentication::class, 'loginPage'])->name('login')->middleware('guest');


require __DIR__.'/auth.php';
