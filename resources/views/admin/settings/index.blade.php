@extends('admin.layouts.admin')

@section('title', 'Settings & Theme Management')

@section('content')
@php
    $s = fn ($key, $default) => old($key, $settings[$key] ?? $default);

    // Admin Panel Color Groups
    $adminColorGroups = [
        'Buttons' => [
            ['btn_primary_bg',    'btnPrimaryBgPicker',    'btnPrimaryBgText',    'Primary button background', 'Save, submit and other primary actions.', '#0f172a'],
            ['btn_primary_text',  'btnPrimaryTextPicker',  'btnPrimaryTextText',  'Primary button text',       'Text and icon colour inside primary buttons.', '#ffffff'],
            ['btn_primary_hover', 'btnPrimaryHoverPicker', 'btnPrimaryHoverText', 'Primary button hover',      'Background while hovering a primary button.', '#1e293b'],
            ['btn_accent_bg',     'btnAccentBgPicker',     'btnAccentBgText',     'Accent button background',  'Add, create and success actions.', '#10b981'],
            ['btn_accent_text',   'btnAccentTextPicker',   'btnAccentTextText',   'Accent button text',        'Text and icon colour inside accent buttons.', '#ffffff'],
        ],
        'Sidebar' => [
            ['sidebar_bg_color',     'sidebarBgPicker',     'sidebarBgText',     'Sidebar background',  'Background of the left navigation.', '#000000'],
            ['sidebar_active_color', 'sidebarActivePicker', 'sidebarActiveText', 'Active menu item',    'Highlight for the current page link.', '#add8e6'],
            ['sidebar_text_color',   'sidebarTextPicker',   'sidebarTextText',   'Sidebar text',        'Menu labels and icons.', '#ffffff'],
        ],
        'Brand' => [
            ['theme_primary_color', 'primaryColorPicker', 'primaryColorText', 'Brand colour',        'Focus rings and brand highlights across the panel.', '#0f172a'],
            ['theme_hover_color',   'hoverColorPicker',   'hoverColorText',   'Hover / link colour', 'Links and subtle interactive states.', '#334155'],
        ],
    ];

    $adminPresets = [
        ['Midnight slate', 'Classic dark and clean', ['#0F172A', '#1E293B', '#10B981', '#000000', '#ADD8E6', '#0F172A', '#334155']],
        ['Emerald fresh',  'Modern green accent',    ['#059669', '#047857', '#0284C7', '#064E3B', '#6EE7B7', '#059669', '#047857']],
        ['Royal indigo',   'Modern tech & deep blue',['#4338CA', '#3730A3', '#06B6D4', '#1E1B4B', '#C7D2FE', '#4338CA', '#3730A3']],
        ['Luxury violet',  'Premium violet tone',    ['#7C3AED', '#6D28D9', '#EC4899', '#0F172A', '#E9D5FF', '#7C3AED', '#6D28D9']],
        ['Amber gold',     'Warm & vibrant',         ['#D97706', '#B45309', '#EF4444', '#18181B', '#FDE68A', '#D97706', '#B45309']],
    ];
@endphp

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                <i class="ph-duotone ph-gear-six text-primary"></i> {{ __('Settings & Customization') }}
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                {{ __('Manage system brand name, color palettes, dark mode preferences, and footer credits.') }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.settings.reset') }}" onclick="return confirm('Are you sure you want to reset all settings to defaults?')" class="btn btn-outline text-rose-600 hover:text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-900/50 hover:bg-rose-50 dark:hover:bg-rose-950/20">
                <i class="ph ph-arrow-counter-clockwise"></i> {{ __('Reset Defaults') }}
            </a>
            <button type="submit" form="settingsForm" class="btn btn-primary">
                <i class="ph ph-floppy-disk"></i> {{ __('Save Changes') }}
            </button>
        </div>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST" id="settingsForm" class="space-y-6">
        @csrf

        {{-- General System & Brand Settings --}}
        <div class="card p-6">
            <div class="border-b border-slate-100 dark:border-slate-800 pb-4 mb-5">
                <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="ph-duotone ph-buildings text-primary"></i> {{ __('General & Brand Settings') }}
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">{{ __('Basic application identity and appearance.') }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="store_name" class="form-label font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-300">
                        {{ __('Application / Console Name') }} <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="store_name" name="store_name" value="{{ $s('store_name', 'Admin Console') }}" required maxlength="60" class="form-control mt-1.5 font-medium">
                    <p class="text-[11.5px] text-slate-400 mt-1">{{ __('Appears in page titles, header branding, and browser tab titles.') }}</p>
                </div>

                <div>
                    <label for="theme_mode" class="form-label font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-300">
                        {{ __('Default Theme Mode') }}
                    </label>
                    <select id="theme_mode" name="theme_mode" class="form-control mt-1.5 font-medium">
                        <option value="system" {{ $s('theme_mode', 'system') === 'system' ? 'selected' : '' }}>{{ __('System Preference (Auto)') }}</option>
                        <option value="light" {{ $s('theme_mode', 'system') === 'light' ? 'selected' : '' }}>{{ __('Light Mode') }}</option>
                        <option value="dark" {{ $s('theme_mode', 'system') === 'dark' ? 'selected' : '' }}>{{ __('Dark Mode') }}</option>
                    </select>
                    <p class="text-[11.5px] text-slate-400 mt-1">{{ __('Default color scheme for new visitors and staff.') }}</p>
                </div>
            </div>
        </div>

        {{-- Color Presets --}}
        <div class="card p-6">
            <div class="border-b border-slate-100 dark:border-slate-800 pb-4 mb-5">
                <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="ph-duotone ph-swatches text-primary"></i> {{ __('One-Click Theme Presets') }}
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">{{ __('Quickly switch to a curated color scheme or tweak individual values below.') }}</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3.5">
                @foreach($adminPresets as [$pName, $pDesc, $pColors])
                    <button type="button" class="p-3.5 rounded-2xl border border-slate-200 dark:border-slate-700/80 hover:border-emerald-500 text-left transition hover:shadow-md bg-white dark:bg-slate-900 group" data-apply-preset='@json($pColors)'>
                        <div class="flex items-center gap-1.5 mb-2.5">
                            @foreach(array_slice($pColors, 0, 5) as $c)
                                <span class="w-4 h-4 rounded-full shadow-xs" style="background-color: {{ $c }}"></span>
                            @endforeach
                        </div>
                        <h4 class="text-xs font-bold text-slate-900 dark:text-white group-hover:text-emerald-600 transition">{{ $pName }}</h4>
                        <p class="text-[11px] text-slate-400 truncate">{{ $pDesc }}</p>
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Detailed Color Customization --}}
        @foreach($adminColorGroups as $groupTitle => $groupItems)
            <div class="card p-6">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-4 mb-5">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="ph-duotone ph-paint-brush text-primary"></i> {{ $groupTitle }} {{ __('Colors') }}
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($groupItems as [$key, $pickerId, $textId, $label, $desc, $default])
                        <div class="p-4 rounded-2xl border border-slate-100 dark:border-slate-800/80 bg-slate-50/50 dark:bg-slate-900/40">
                            <label for="{{ $textId }}" class="block text-xs font-bold text-slate-800 dark:text-slate-200">{{ $label }}</label>
                            <p class="text-[11px] text-slate-400 mt-0.5 mb-3">{{ $desc }}</p>

                            <div class="flex items-center gap-2.5">
                                <input type="color" id="{{ $pickerId }}" value="{{ $s($key, $default) }}" class="w-10 h-10 rounded-xl cursor-pointer border-0 bg-transparent p-0">
                                <input type="text" id="{{ $textId }}" name="{{ $key }}" value="{{ $s($key, $default) }}" required maxlength="20" class="form-control font-mono uppercase text-xs !h-10">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        {{-- Footer & Copyright Settings --}}
        <div class="card p-6">
            <div class="border-b border-slate-100 dark:border-slate-800 pb-4 mb-5">
                <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="ph-duotone ph-copyright text-primary"></i> {{ __('Footer & Copyright Settings') }}
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">{{ __('Customize the footer information shown across the admin console.') }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label for="footer_copyright_prefix" class="form-label font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-300">{{ __('Copyright Prefix') }}</label>
                    <input type="text" id="footer_copyright_prefix" name="footer_copyright_prefix" value="{{ $s('footer_copyright_prefix', '© ' . date('Y') . ', made with ❤️ by') }}" class="form-control mt-1.5 text-xs font-medium">
                </div>

                <div>
                    <label for="footer_creator_name" class="form-label font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-300">{{ __('Creator Name') }}</label>
                    <input type="text" id="footer_creator_name" name="footer_creator_name" value="{{ $s('footer_creator_name', 'Decent Infoways') }}" class="form-control mt-1.5 text-xs font-medium">
                </div>

                <div>
                    <label for="footer_creator_url" class="form-label font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-300">{{ __('Creator URL') }}</label>
                    <input type="url" id="footer_creator_url" name="footer_creator_url" value="{{ $s('footer_creator_url', 'https://decentinfoways.com') }}" class="form-control mt-1.5 text-xs font-medium">
                </div>
            </div>
        </div>

        {{-- Bottom Save Bar --}}
        <div class="flex items-center justify-end gap-3 pt-2">
            <button type="submit" class="btn btn-primary !px-8">
                <i class="ph ph-floppy-disk"></i> {{ __('Save Changes') }}
            </button>
        </div>

    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Sync color pickers and text inputs
        document.querySelectorAll('input[type="color"]').forEach(function (picker) {
            var textInput = document.getElementById(picker.id.replace('Picker', 'Text'));
            if (!textInput) return;

            picker.addEventListener('input', function () {
                textInput.value = picker.value.toUpperCase();
            });

            textInput.addEventListener('input', function () {
                if (/^#[0-9A-F]{6}$/i.test(textInput.value)) {
                    picker.value = textInput.value;
                }
            });
        });

        // Apply presets
        document.querySelectorAll('[data-apply-preset]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var colors = JSON.parse(btn.getAttribute('data-apply-preset'));
                // Order: primary, hover, accent, sidebar_bg, sidebar_active, btn_pri_bg, btn_pri_hover
                var map = [
                    ['btn_primary_bg', colors[0]],
                    ['btn_primary_hover', colors[1]],
                    ['btn_accent_bg', colors[2]],
                    ['sidebar_bg_color', colors[3]],
                    ['sidebar_active_color', colors[4]],
                    ['theme_primary_color', colors[5] || colors[0]],
                    ['theme_hover_color', colors[6] || colors[1]],
                ];

                map.forEach(function (pair) {
                    var input = document.querySelector('input[name="' + pair[0] + '"]');
                    if (input && pair[1]) {
                        input.value = pair[1].toUpperCase();
                        var picker = document.querySelector('input[type="color"]#' + input.id.replace('Text', 'Picker'));
                        if (picker) picker.value = pair[1];
                    }
                });
            });
        });
    });
</script>
@endpush
@endsection
