<?php

namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalRoles = Role::count();
        $adminUsers = User::role('admin')->count();
        $recentUsers = User::latest()->take(5)->get();

        return view('dashboard', compact(
            'totalUsers',
            'totalRoles',
            'adminUsers',
            'recentUsers'
        ));
    }
}
