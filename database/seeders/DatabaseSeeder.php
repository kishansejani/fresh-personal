<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Seed Settings
        Setting::set('store_name', 'Admin Console', 'general');
        Setting::set('theme_mode', 'system', 'theme');
        Setting::set('theme_primary_color', '#000000', 'theme');
        Setting::set('theme_hover_color', '#a1a1a1', 'theme');
        Setting::set('btn_primary_bg', '#0f172a', 'theme');
        Setting::set('btn_primary_text', '#ffffff', 'theme');
        Setting::set('btn_primary_hover', '#1e293b', 'theme');
        Setting::set('btn_accent_bg', '#10b981', 'theme');
        Setting::set('btn_accent_text', '#ffffff', 'theme');
        Setting::set('sidebar_bg_color', '#000000', 'theme');
        Setting::set('sidebar_active_color', '#add8e6', 'theme');
        Setting::set('sidebar_text_color', '#ffffff', 'theme');
        Setting::set('footer_copyright_prefix', '© ' . date('Y') . ', made with ❤️ by', 'footer');
        Setting::set('footer_creator_name', 'Decent Infoways', 'footer');
        Setting::set('footer_creator_url', 'https://decentinfoways.com', 'footer');

        // 1. Create Administration Permissions
        $permissionsList = [
            ['name' => 'manage_users', 'display_name' => 'Manage Users & Accounts', 'group' => 'administration'],
            ['name' => 'manage_roles', 'display_name' => 'Manage Roles & Permissions', 'group' => 'administration'],
            ['name' => 'manage_settings', 'display_name' => 'Manage System & Theme Settings', 'group' => 'administration'],
        ];

        $allPermissionIds = [];
        foreach ($permissionsList as $p) {
            $perm = Permission::firstOrCreate(['name' => $p['name']], $p);
            $allPermissionIds[] = $perm->id;
        }

        // 2. Create Roles
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'super_admin'],
            ['display_name' => 'Super Admin', 'description' => 'Full control over all administrative modules, users, roles and settings']
        );

        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['display_name' => 'Administrator', 'description' => 'Can manage users, roles and settings']
        );

        $userRole = Role::firstOrCreate(
            ['name' => 'user'],
            ['display_name' => 'Standard User', 'description' => 'Basic staff user']
        );

        $superAdminRole->permissions()->sync($allPermissionIds);
        $adminRole->permissions()->sync($allPermissionIds);

        // 3. Create Default Users
        User::firstOrCreate(
            ['email' => 'superadmin@grocery.com'],
            [
                'name' => 'Super Administrator',
                'phone' => '9876500000',
                'password' => Hash::make('admin123'),
                'role' => 'super_admin',
                'role_id' => $superAdminRole->id,
                'language' => 'en',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin@grocery.com'],
            [
                'name' => 'Store Administrator',
                'phone' => '9876543210',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'role_id' => $adminRole->id,
                'language' => 'en',
                'is_active' => true,
            ]
        );
    }
}
