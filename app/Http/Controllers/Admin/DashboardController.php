<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $inactiveUsers = User::where('is_active', false)->count();
        $totalRoles = Role::count();
        $totalPermissions = Permission::count();
        
        $superAdminsCount = User::where('role', 'super_admin')->count();
        $adminsCount = User::where('role', 'admin')->count();
        $staffCount = User::whereNotIn('role', ['super_admin', 'admin'])->count();

        $recentUsers = User::with('roleModel')->latest()->take(8)->get();
        $rolesWithCount = Role::withCount('users')->get();

        $settings = Setting::getAllSettings();

        $systemInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_environment' => app()->environment(),
            'db_connection' => config('database.default'),
            'db_name' => config('database.connections.mysql.database'),
            'timezone' => config('app.timezone'),
        ];

        return view('admin.dashboard', compact(
            'totalUsers',
            'activeUsers',
            'inactiveUsers',
            'totalRoles',
            'totalPermissions',
            'superAdminsCount',
            'adminsCount',
            'staffCount',
            'recentUsers',
            'rolesWithCount',
            'settings',
            'systemInfo'
        ));
    }
}
