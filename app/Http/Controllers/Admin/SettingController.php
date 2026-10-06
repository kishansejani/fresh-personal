<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::getAllSettings();
        $isSuperAdmin = Auth::user() && Auth::user()->isSuperAdmin();
        return view('admin.settings.index', compact('settings', 'isSuperAdmin'));
    }

    public function update(Request $request)
    {
        $isSuperAdmin = Auth::user() && Auth::user()->isSuperAdmin();

        $rules = [
            'theme_primary_color' => 'required|string|max:20',
            'theme_hover_color' => 'required|string|max:20',
            'btn_primary_bg' => 'required|string|max:20',
            'btn_primary_text' => 'required|string|max:20',
            'btn_primary_hover' => 'required|string|max:20',
            'btn_accent_bg' => 'required|string|max:20',
            'btn_accent_text' => 'required|string|max:20',
            'sidebar_bg_color' => 'required|string|max:20',
            'sidebar_active_color' => 'required|string|max:20',
            'sidebar_text_color' => 'nullable|string|max:20',
            'footer_copyright_prefix' => 'nullable|string|max:100',
            'footer_creator_name' => 'nullable|string|max:100',
            'footer_creator_url' => 'nullable|url|max:255',
            'theme_mode' => 'nullable|in:light,dark,system',
            'store_name' => 'nullable|string|max:60',
        ];

        // Frontend Web Colors (Light + Dark)
        $frontFields = [
            // Light
            'front_brand_primary', 'front_brand_hover', 'front_brand_light', 'front_accent_color',
            'front_btn_primary_bg', 'front_btn_primary_text', 'front_btn_primary_hover',
            'front_btn_accent_bg', 'front_btn_accent_text',
            'front_topbar_bg', 'front_topbar_text', 'front_header_bg',
            'front_body_bg', 'front_card_bg', 'front_card_border',
            'front_footer_bg', 'front_footer_text',
            'front_text_primary', 'front_text_muted',
            'front_input_bg', 'front_input_border', 'front_dropdown_bg',
            // Dark
            'front_dark_brand_primary', 'front_dark_brand_hover', 'front_dark_brand_light', 'front_dark_accent_color',
            'front_dark_btn_primary_bg', 'front_dark_btn_primary_text', 'front_dark_btn_primary_hover',
            'front_dark_body_bg', 'front_dark_card_bg', 'front_dark_card_border',
            'front_dark_header_bg', 'front_dark_topbar_bg', 'front_dark_topbar_text',
            'front_dark_footer_bg', 'front_dark_footer_text',
            'front_dark_text_primary', 'front_dark_text_muted',
            'front_dark_input_bg', 'front_dark_input_border', 'front_dark_dropdown_bg',
        ];

        foreach ($frontFields as $ff) {
            $rules[$ff] = 'nullable|string|max:30';
        }

        $request->validate($rules);

        // Save Admin Settings
        Setting::set('theme_primary_color', $request->theme_primary_color, 'theme');
        Setting::set('theme_hover_color', $request->theme_hover_color, 'theme');
        Setting::set('btn_primary_bg', $request->btn_primary_bg, 'theme');
        Setting::set('btn_primary_text', $request->btn_primary_text, 'theme');
        Setting::set('btn_primary_hover', $request->btn_primary_hover, 'theme');
        Setting::set('btn_accent_bg', $request->btn_accent_bg, 'theme');
        Setting::set('btn_accent_text', $request->btn_accent_text, 'theme');
        Setting::set('sidebar_bg_color', $request->sidebar_bg_color, 'theme');
        Setting::set('sidebar_active_color', $request->sidebar_active_color, 'theme');
        Setting::set('sidebar_text_color', $request->sidebar_text_color ?? '#ffffff', 'theme');
        Setting::set('footer_copyright_prefix', $request->footer_copyright_prefix ?? '© ' . date('Y') . ', made with ❤️ by', 'footer');
        Setting::set('footer_creator_name', $request->footer_creator_name ?? 'Decent Infoways', 'footer');
        Setting::set('footer_creator_url', $request->footer_creator_url ?? 'https://decentinfoways.com', 'footer');
        Setting::set('store_name', $request->store_name ?: 'Admin Console', 'general');

        if ($request->filled('theme_mode')) {
            Setting::set('theme_mode', $request->theme_mode, 'theme');
        }

        // Save Frontend & Dark Colors
        foreach ($frontFields as $ff) {
            if ($request->filled($ff)) {
                Setting::set($ff, $request->input($ff), 'front_theme');
            }
        }

        return redirect()->route('admin.settings.index')->with('success', 'Settings & Color Management saved successfully!');
    }

    public function reset()
    {
        Setting::truncate();
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'DatabaseSeeder']);

        return redirect()->route('admin.settings.index')->with('success', 'Settings have been reset to default values.');
    }
}
