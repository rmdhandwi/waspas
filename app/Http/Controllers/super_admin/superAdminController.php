<?php

namespace App\Http\Controllers\super_admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class superAdminController extends Controller
{
    //
    public function Dashboard()
    {
        return Inertia::render('SuperAdmin/Dashboard');
    }
}
