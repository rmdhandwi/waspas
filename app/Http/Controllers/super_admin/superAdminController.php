<?php

namespace App\Http\Controllers\super_admin;

use App\Http\Controllers\Controller;
use App\Models\Kriteria;
use App\Models\User;
use Inertia\Inertia;

class superAdminController extends Controller
{
    //
    public function Dashboard()
    {
        $countKriteria = Kriteria::count();
        $countUsers = User::count();
        return Inertia::render('SuperAdmin/Dashboard', ['countKriteria' => $countKriteria, 'countUsers' => $countUsers]);
    }

    public function UsersPage()
    {
        $user = User::where('role','admin')->select('id','username','nama','role','foto_profil')->get();
        return Inertia::render('SuperAdmin/Users/Users', ['usersData' => $user ]);
    }
}
