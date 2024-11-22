<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\dataWarga;
use Illuminate\Http\Request;
use Inertia\Inertia;

class adminController extends Controller
{
    //
    public function Dashboard()
    {
        $countDataWarga = dataWarga::count();
        return Inertia::render('Admin/Dashboard',['countDataWarga' => $countDataWarga]);
    }
}
