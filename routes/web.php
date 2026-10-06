<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Admin\AuthController as AdminAuth;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\RoleController as AdminRole;
use App\Http\Controllers\Admin\UserController as AdminUser;
use App\Http\Controllers\Admin\SettingController as AdminSetting;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Root redirect directly to Admin Dashboard or Login
Route::get('/', function () {
    if (\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('admin.login');
})->name('home');

// Language Switcher
Route::get('/lang/{locale}', [LanguageController::class, 'switch'])->name('lang.switch');

/*
|--------------------------------------------------------------------------
| Admin Portal Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        if (\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('admin.login');
    })->name('index');

    Route::get('/login', [AdminAuth::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuth::class, 'login'])->name('login.post');
    Route::post('/logout', [AdminAuth::class, 'logout'])->name('logout');

    Route::middleware(['admin'])->group(function () {
        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

        // Administration: Roles & Permissions Management
        Route::resource('roles', AdminRole::class)->middleware('permission:manage_roles');

        // Administration: Users Management
        Route::middleware('permission:manage_users')->group(function () {
            Route::resource('users', AdminUser::class);
            Route::patch('users/{user}/toggle-status', [AdminUser::class, 'toggleStatus'])->name('users.toggle-status');
        });

        // Administration: System Settings & Theme Customization
        Route::middleware('permission:manage_settings')->group(function () {
            Route::get('/settings', [AdminSetting::class, 'index'])->name('settings.index');
            Route::post('/settings', [AdminSetting::class, 'update'])->name('settings.update');
            Route::get('/settings/reset', [AdminSetting::class, 'reset'])->name('settings.reset');
        });
    });
});
