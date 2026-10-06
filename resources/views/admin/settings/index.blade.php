@extends('admin.layouts.admin')

@section('title', 'Settings & Color Management')

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

    // Frontend Web Colors — Light Mode
    $frontLightGroups = [
        'Brand & Accents' => [
            ['front_brand_primary', 'fBrandPriPicker',   'fBrandPriText',   'Brand primary',      'Main header accents, active badges & highlights.', '#059669'],
            ['front_brand_hover',   'fBrandHovPicker',   'fBrandHovText',   'Brand hover',        'Hover state for brand links and elements.', '#047857'],
            ['front_brand_light',   'fBrandLtPicker',    'fBrandLtText',    'Brand soft tint',    'Soft background pill & badge tint.', '#ecfdf5'],
            ['front_accent_color',  'fAccentPicker',     'fAccentText',     'Accent highlight',   'Selected dropdowns, icons & steppers.', '#10b981'],
        ],
        'Buttons' => [
            ['front_btn_primary_bg',    'fBtnPriBgPicker',  'fBtnPriBgText',  'Primary button bg',    'Add to cart, checkout, primary CTA buttons.', '#059669'],
            ['front_btn_primary_text',  'fBtnPriTxtPicker', 'fBtnPriTxtText', 'Primary button text',  'Text & icon colour on primary buttons.', '#ffffff'],
            ['front_btn_primary_hover', 'fBtnPriHovPicker', 'fBtnPriHovText', 'Primary button hover', 'Hover background on primary buttons.', '#047857'],
            ['front_btn_accent_bg',     'fBtnAccBgPicker',  'fBtnAccBgText',  'Secondary button bg',  'Stepper buttons & secondary actions.', '#10b981'],
            ['front_btn_accent_text',   'fBtnAccTxtPicker', 'fBtnAccTxtText', 'Secondary button text','Text on secondary & stepper buttons.', '#ffffff'],
        ],
        'Surfaces & Layout' => [
            ['front_topbar_bg',    'fTopBgPicker',     'fTopBgText',     'Utility topbar bg',    'Top promo & language/theme utility bar.', '#064e3b'],
            ['front_topbar_text',  'fTopTxtPicker',    'fTopTxtText',    'Utility topbar text',  'Text & icons inside top utility bar.', '#ecfdf5'],
            ['front_header_bg',    'fHeadBgPicker',    'fHeadBgText',    'Main header bg',       'Sticky top navigation & search bar header.', '#ffffff'],
            ['front_body_bg',      'fBodyBgPicker',    'fBodyBgText',    'Page background',      'Storefront body surface background.', '#f6f8f7'],
            ['front_card_bg',      'fCardBgPicker',    'fCardBgText',    'Card & box surface',   'Product cards, checkout panels & drawers.', '#ffffff'],
            ['front_card_border',  'fCardBrdPicker',   'fCardBrdText',   'Card & line border',   'Dividers, borders & container outlines.', '#e2e8f0'],
            ['front_footer_bg',    'fFootBgPicker',    'fFootBgText',    'Footer background',    'Main footer area at page bottom.', '#0f172a'],
            ['front_footer_text',  'fFootTxtPicker',   'fFootTxtText',   'Footer text',          'Links, categories & copyright text.', '#94a3b8'],
        ],
        'Text & Form Controls' => [
            ['front_text_primary', 'fTxtPriPicker',    'fTxtPriText',    'Primary text',         'Product titles, headings & primary text.', '#0f172a'],
            ['front_text_muted',   'fTxtMutPicker',    'fTxtMutText',    'Muted / hint text',    'Subtitles, timestamps & placeholder text.', '#64748b'],
            ['front_input_bg',     'fInpBgPicker',     'fInpBgText',     'Input & search bg',    'Search input, checkout form fields.', '#ffffff'],
            ['front_input_border', 'fInpBrdPicker',    'fInpBrdText',    'Input field border',   'Form input & select borders.', '#cbd5e1'],
            ['front_dropdown_bg',  'fDropBgPicker',    'fDropBgText',    'Dropdown menu bg',     'All categories menu & custom dropdowns.', '#ffffff'],
        ],
    ];

    // Frontend Web Colors — Dark Mode
    $frontDarkGroups = [
        'Dark Brand & Accents' => [
            ['front_dark_brand_primary', 'fdBrandPriPicker', 'fdBrandPriText', 'Dark brand primary',   'Main header accents & highlights in dark mode.', '#34d399'],
            ['front_dark_brand_hover',   'fdBrandHovPicker', 'fdBrandHovText', 'Dark brand hover',     'Hover state for links in dark mode.', '#6ee7b7'],
            ['front_dark_brand_light',   'fdBrandLtPicker',  'fdBrandLtText',  'Dark brand soft tint', 'Soft background tint in dark mode.', '#064e3b'],
            ['front_dark_accent_color',  'fdAccentPicker',   'fdAccentText',   'Dark accent highlight','Selected items & stepper in dark mode.', '#10b981'],
        ],
        'Dark Buttons' => [
            ['front_dark_btn_primary_bg',    'fdBtnPriBgPicker',  'fdBtnPriBgText',  'Dark primary btn bg',   'Primary button background in dark mode.', '#059669'],
            ['front_dark_btn_primary_text',  'fdBtnPriTxtPicker', 'fdBtnPriTxtText', 'Dark primary btn text', 'Text on primary button in dark mode.', '#ffffff'],
            ['front_dark_btn_primary_hover', 'fdBtnPriHovPicker', 'fdBtnPriHovText', 'Dark primary btn hover','Hover background in dark mode.', '#10b981'],
        ],
        'Dark Surfaces & Layout' => [
            ['front_dark_body_bg',      'fdBodyBgPicker',   'fdBodyBgText',   'Dark page background', 'Storefront dark mode body background.', '#020617'],
            ['front_dark_card_bg',      'fdCardBgPicker',   'fdCardBgText',   'Dark card surface',    'Product cards & panels in dark mode.', '#0f172a'],
            ['front_dark_card_border',  'fdCardBrdPicker',  'fdCardBrdText',  'Dark card border',     'Container & divider borders in dark mode.', '#1e293b'],
            ['front_dark_header_bg',    'fdHeadBgPicker',   'fdHeadBgText',   'Dark header bg',       'Sticky header background in dark mode.', '#0b1120'],
            ['front_dark_topbar_bg',    'fdTopBgPicker',    'fdTopBgText',    'Dark topbar bg',       'Top utility bar background in dark mode.', '#020617'],
            ['front_dark_topbar_text',  'fdTopTxtPicker',   'fdTopTxtText',   'Dark topbar text',     'Top utility bar text in dark mode.', '#94a3b8'],
            ['front_dark_footer_bg',    'fdFootBgPicker',   'fdFootBgText',   'Dark footer bg',       'Footer surface in dark mode.', '#020617'],
            ['front_dark_footer_text',  'fdFootTxtPicker',  'fdFootTxtText',  'Dark footer text',     'Footer links & copyright in dark mode.', '#64748b'],
        ],
        'Dark Text & Form Controls' => [
            ['front_dark_text_primary', 'fdTxtPriPicker',   'fdTxtPriText',   'Dark primary text',    'Headings and product names in dark mode.', '#f1f5f9'],
            ['front_dark_text_muted',   'fdTxtMutPicker',   'fdTxtMutText',   'Dark muted text',      'Subtext and labels in dark mode.', '#94a3b8'],
            ['front_dark_input_bg',     'fdInpBgPicker',    'fdInpBgText',    'Dark input bg',        'Search bar & form field bg in dark mode.', '#0b1324'],
            ['front_dark_input_border', 'fdInpBrdPicker',   'fdInpBrdText',   'Dark input border',    'Input field borders in dark mode.', '#334155'],
            ['front_dark_dropdown_bg',  'fdDropBgPicker',   'fdDropBgText',   'Dark dropdown menu bg','Dropdown menus & popovers in dark mode.', '#0f172a'],
        ],
    ];

    $adminPresets = [
        ['Midnight slate', 'Classic dark and clean', ['#0F172A', '#1E293B', '#10B981', '#000000', '#ADD8E6', '#0F172A', '#334155']],
        ['Emerald fresh',  'Grocery and organic',    ['#059669', '#047857', '#0284C7', '#064E3B', '#6EE7B7', '#059669', '#047857']],
        ['Royal indigo',   'Modern tech',            ['#4338CA', '#3730A3', '#06B6D4', '#1E1B4B', '#C7D2FE', '#4338CA', '#3730A3']],
        ['Luxury violet',  'Premium and bold',       ['#7C3AED', '#6D28D9', '#EC4899', '#0F172A', '#E9D5FF', '#7C3AED', '#6D28D9']],
        ['Amber gold',     'Warm e-commerce',        ['#D97706', '#B45309', '#EF4444', '#18181B', '#FDE68A', '#D97706', '#B45309']],
    ];
@endphp

    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header-main">
            <div class="page-header-icon"><i class="ph-duotone ph-gear-six"></i></div>
            <div>
                <h1 class="page-title">{{ __('Settings & Color Management') }}</h1>
                <p class="page-subtitle">{{ __('Centralized color system, branding and appearance controls across the entire system.') }}</p>
            </div>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.settings.reset') }}" onclick="return confirm('Are you sure you want to reset settings to defaults?')" class="btn btn-outline">
                <i class="ph ph-arrow-counter-clockwise"></i> {{ __('Reset Defaults') }}
            </a>
            <button type="submit" form="settingsForm" class="btn btn-primary">
                <i class="ph-bold ph-floppy-disk"></i> {{ __('Save Settings') }}
            </button>
        </div>
    </div>

    {{-- Module Switcher Tabs --}}
    <div class="flex items-center gap-2 border-b border-slate-200 dark:border-slate-800 pb-3 mb-6 overflow-x-auto">
        <button type="button" class="tab-btn is-active flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition" data-target-tab="tab-admin">
            <i class="ph-duotone ph-shield-check text-base"></i>
            <span>{{ __('Admin Console Theme') }}</span>
        </button>
        <button type="button" class="tab-btn flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition" data-target-tab="tab-front">
            <i class="ph-duotone ph-storefront text-base"></i>
            <span>{{ __('Frontend Web Colors') }}</span>
            <span class="badge badge-neutral text-[9.5px] uppercase tracking-wider">{{ __('Super Admin') }}</span>
        </button>
        <button type="button" class="tab-btn flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition" data-target-tab="tab-dark">
            <i class="ph-duotone ph-moon-stars text-base"></i>
            <span>{{ __('Dark Mode Colors') }}</span>
            <span class="badge badge-neutral text-[9.5px] uppercase tracking-wider">{{ __('Dark Theme') }}</span>
        </button>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST" id="settingsForm">
        @csrf

        {{-- TAB 1: ADMIN CONSOLE THEME --}}
        <div id="tab-admin" class="settings-tab">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                
                {{-- Left Side: Controls --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- General Details --}}
                    <div class="card">
                        <div class="card-header">
                            <div>
                                <h3 class="card-title"><i class="ph-duotone ph-storefront"></i> {{ __('General Details') }}</h3>
                                <p class="card-subtitle">{{ __('Shown in the sidebar, browser tab title, invoices and exports.') }}</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <label for="storeName" class="form-label">{{ __('Store / Panel Name') }}</label>
                            <input type="text" id="storeName" name="store_name" value="{{ $s('store_name', 'Fresh Express') }}" required maxlength="60" class="form-control font-medium">
                            <p class="form-hint mt-1.5">{{ __('Displayed across the top navigation and storefront.') }}</p>
                        </div>
                    </div>

                    {{-- Admin Theme Presets --}}
                    <div class="card">
                        <div class="card-header">
                            <div>
                                <h3 class="card-title"><i class="ph-duotone ph-swatches"></i> {{ __('Admin Theme Presets') }}</h3>
                                <p class="card-subtitle">{{ __('Apply a curated palette to the admin console in one click.') }}</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
                                @foreach($adminPresets as [$pName, $pDesc, $pColors])
                                    <button type="button" class="preset-card text-left p-3.5 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-slate-400 bg-white dark:bg-slate-900 transition" onclick="applyAdminPreset('{{ $pColors[0] }}', '{{ $pColors[1] }}', '{{ $pColors[2] }}', '{{ $pColors[3] }}', '{{ $pColors[4] }}', '{{ $pColors[5] }}', '{{ $pColors[6] }}')">
                                        <div class="flex items-center gap-1 mb-2.5">
                                            @foreach(array_slice($pColors, 0, 5) as $c)
                                                <span class="w-4 h-4 rounded-full" style="background: {{ $c }}"></span>
                                            @endforeach
                                        </div>
                                        <h4 class="text-xs font-bold text-slate-900 dark:text-white">{{ $pName }}</h4>
                                        <p class="text-[11px] text-slate-400 truncate">{{ $pDesc }}</p>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Admin Color Pickers --}}
                    @foreach($adminColorGroups as $groupName => $fields)
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"><i class="ph-duotone ph-palette"></i> {{ $groupName }} {{ __('Colours') }}</h3>
                            </div>
                            <div class="card-body grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($fields as [$name, $pickerId, $textId, $label, $desc, $default])
                                    <div class="p-3 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/30">
                                        <label for="{{ $textId }}" class="form-label !mb-0.5 text-xs font-bold">{{ $label }}</label>
                                        <p class="text-[11px] text-slate-400 mb-2.5">{{ $desc }}</p>
                                        <div class="flex items-center gap-2">
                                            <input type="color" id="{{ $pickerId }}" value="{{ $s($name, $default) }}" class="w-9 h-9 rounded-lg border-0 p-0 cursor-pointer bg-transparent">
                                            <input type="text" id="{{ $textId }}" name="{{ $name }}" value="{{ $s($name, $default) }}" required maxlength="20" class="form-control font-mono uppercase text-xs !h-9">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    {{-- Footer & Copyright --}}
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="ph-duotone ph-copyright"></i> {{ __('Footer Details') }}</h3>
                        </div>
                        <div class="card-body grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label for="footerPrefix" class="form-label">{{ __('Copyright prefix') }}</label>
                                <input type="text" id="footerPrefix" name="footer_copyright_prefix" value="{{ $s('footer_copyright_prefix', '© ' . date('Y') . ', made with ❤️ by') }}" class="form-control text-xs">
                            </div>
                            <div>
                                <label for="footerCreator" class="form-label">{{ __('Creator name') }}</label>
                                <input type="text" id="footerCreator" name="footer_creator_name" value="{{ $s('footer_creator_name', 'Decent Infoways') }}" class="form-control text-xs">
                            </div>
                            <div>
                                <label for="footerUrl" class="form-label">{{ __('Creator URL') }}</label>
                                <input type="url" id="footerUrl" name="footer_creator_url" value="{{ $s('footer_creator_url', 'https://decentinfoways.com') }}" class="form-control text-xs">
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Right Side: Live Admin Preview Widget --}}
                <div class="space-y-6 lg:sticky lg:top-24">
                    <div class="card overflow-hidden">
                        <div class="card-header bg-slate-50 dark:bg-slate-800/50">
                            <h3 class="card-title text-xs"><i class="ph-duotone ph-eye"></i> {{ __('Admin Preview') }}</h3>
                            <span class="badge badge-success text-[10px]">{{ __('Live') }}</span>
                        </div>
                        <div class="p-4 bg-slate-100 dark:bg-slate-950 flex items-center justify-center">
                            
                            {{-- Simulated Mockup --}}
                            <div class="w-full max-w-sm rounded-2xl overflow-hidden shadow-xl border border-slate-300 dark:border-slate-800 bg-white dark:bg-slate-900 grid grid-cols-[85px_1fr] text-[10px]">
                                {{-- Preview Sidebar --}}
                                <div id="pvSidebar" class="p-2.5 flex flex-col justify-between" style="background: var(--sidebar-bg); color: var(--sidebar-text);">
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-1 font-bold text-[11px] truncate" id="pvStoreName">
                                            <i class="ph-fill ph-basket"></i> <span>{{ $s('store_name', 'Fresh Express') }}</span>
                                        </div>
                                        <div class="space-y-1 pt-1">
                                            <div class="p-1 rounded-md opacity-70 flex items-center gap-1"><i class="ph ph-squares-four"></i> Dashboard</div>
                                            <div class="p-1 rounded-md font-bold flex items-center gap-1" id="pvActiveMenuItem" style="background: var(--sidebar-active); color: var(--sidebar-active-text);"><i class="ph ph-shopping-cart-simple"></i> Orders</div>
                                            <div class="p-1 rounded-md opacity-70 flex items-center gap-1"><i class="ph ph-package"></i> Products</div>
                                            <div class="p-1 rounded-md opacity-70 flex items-center gap-1"><i class="ph ph-users-three"></i> Users</div>
                                        </div>
                                    </div>
                                    <div class="pt-2 border-t border-white/10 opacity-75 truncate">
                                        Admin User
                                    </div>
                                </div>

                                {{-- Preview Content Area --}}
                                <div class="p-3 space-y-2.5 bg-slate-50 dark:bg-slate-900/60">
                                    <div class="h-2 w-16 rounded bg-slate-200 dark:bg-slate-700"></div>
                                    <a href="javascript:void(0)" id="pvLink" class="font-bold underline block" style="color: var(--theme-hover);">View all orders</a>
                                    <div class="flex gap-1.5 pt-1">
                                        <button type="button" id="pvBtnPrimary" class="px-2.5 py-1 rounded-md font-bold shadow-xs text-[9px]" style="background: var(--btn-primary-bg); color: var(--btn-primary-text);">
                                            <i class="ph ph-floppy-disk"></i> Save
                                        </button>
                                        <button type="button" id="pvBtnAccent" class="px-2.5 py-1 rounded-md font-bold shadow-xs text-[9px]" style="background: var(--btn-accent-bg); color: var(--btn-accent-text);">
                                            <i class="ph ph-plus"></i> Add
                                        </button>
                                    </div>
                                    <div class="p-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 space-y-1">
                                        <div class="h-1.5 w-12 rounded bg-slate-200 dark:bg-slate-700"></div>
                                        <div class="h-3 w-full rounded border border-slate-200 dark:border-slate-700"></div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="p-3 bg-slate-50 dark:bg-slate-800/40 border-t border-slate-100 dark:border-slate-800 text-[11px] text-slate-500 text-center">
                            {{ __('The real sidebar, headers, and buttons update automatically as you edit color hex codes.') }}
                        </div>
                    </div>

                    {{-- Sticky Action Buttons --}}
                    <div class="card p-4 flex gap-2.5">
                        <a href="{{ route('admin.settings.reset') }}" class="btn btn-outline flex-1 justify-center" onclick="return confirm('Reset all settings?')">
                            <i class="ph ph-arrow-counter-clockwise"></i> {{ __('Reset') }}
                        </a>
                        <button type="submit" class="btn btn-primary flex-1 justify-center">
                            <i class="ph-bold ph-floppy-disk"></i> {{ __('Save Settings') }}
                        </button>
                    </div>
                </div>

            </div>
        </div>

        {{-- TAB 2: FRONTEND WEB COLORS --}}
        <div id="tab-front" class="settings-tab hidden space-y-6">
            @foreach($frontLightGroups as $groupTitle => $groupFields)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="ph-duotone ph-paint-brush"></i> {{ $groupTitle }}</h3>
                    </div>
                    <div class="card-body grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($groupFields as [$key, $pickerId, $textId, $label, $desc, $default])
                            <div class="p-3 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/30">
                                <label for="{{ $textId }}" class="form-label !mb-0.5 text-xs font-bold">{{ $label }}</label>
                                <p class="text-[11px] text-slate-400 mb-2.5">{{ $desc }}</p>
                                <div class="flex items-center gap-2">
                                    <input type="color" id="{{ $pickerId }}" value="{{ $s($key, $default) }}" class="w-9 h-9 rounded-lg border-0 p-0 cursor-pointer bg-transparent">
                                    <input type="text" id="{{ $textId }}" name="{{ $key }}" value="{{ $s($key, $default) }}" maxlength="30" class="form-control font-mono uppercase text-xs !h-9">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        {{-- TAB 3: DARK MODE COLORS --}}
        <div id="tab-dark" class="settings-tab hidden space-y-6">
            @foreach($frontDarkGroups as $groupTitle => $groupFields)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="ph-duotone ph-moon-stars"></i> {{ $groupTitle }}</h3>
                    </div>
                    <div class="card-body grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($groupFields as [$key, $pickerId, $textId, $label, $desc, $default])
                            <div class="p-3 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/30">
                                <label for="{{ $textId }}" class="form-label !mb-0.5 text-xs font-bold">{{ $label }}</label>
                                <p class="text-[11px] text-slate-400 mb-2.5">{{ $desc }}</p>
                                <div class="flex items-center gap-2">
                                    <input type="color" id="{{ $pickerId }}" value="{{ $s($key, $default) }}" class="w-9 h-9 rounded-lg border-0 p-0 cursor-pointer bg-transparent">
                                    <input type="text" id="{{ $textId }}" name="{{ $key }}" value="{{ $s($key, $default) }}" maxlength="30" class="form-control font-mono uppercase text-xs !h-9">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

    </form>

@endsection

@push('styles')
<style>
    .tab-btn { background: var(--surface-2); color: var(--muted); border: 1px solid var(--line); }
    .tab-btn.is-active { background: var(--btn-accent-bg); color: #fff; border-color: var(--btn-accent-bg); }
    .preset-card:hover { box-shadow: 0 8px 24px -8px rgba(0,0,0,.15); transform: translateY(-1px); }
</style>
@endpush

@push('scripts')
<script>
(function () {
    const root = document.documentElement;
    const HEX = /^#([0-9A-Fa-f]{3}|[0-9A-Fa-f]{6})$/;

    function rgb(hex) {
        let h = hex.replace('#', '').trim();
        if (h.length === 3) h = h.split('').map(c => c + c).join('');
        if (!/^[0-9a-fA-F]{6}$/.test(h)) return [15, 23, 42];
        return [parseInt(h.slice(0, 2), 16), parseInt(h.slice(2, 4), 16), parseInt(h.slice(4, 6), 16)];
    }

    function contrast(hex) {
        const [r, g, b] = rgb(hex);
        return ((0.299 * r + 0.587 * g + 0.114 * b) / 255) > 0.6 ? '#0F172A' : '#FFFFFF';
    }

    const adminVars = {
        btn_primary_bg:       (v) => ({ '--btn-primary-bg': v, '--btn-primary-hover': v }),
        btn_primary_text:     (v) => ({ '--btn-primary-text': v }),
        btn_accent_bg:        (v) => ({ '--btn-accent-bg': v }),
        btn_accent_text:      (v) => ({ '--btn-accent-text': v }),
        sidebar_bg_color:     (v) => ({ '--sidebar-bg': v, '--c-sidebar-text': rgb(contrast(v)).join(' ') }),
        sidebar_active_color: (v) => ({ '--sidebar-active': v, '--sidebar-active-text': contrast(v) }),
        sidebar_text_color:   (v) => ({ '--sidebar-text': v }),
        theme_primary_color:  (v) => ({ '--theme-primary': v, '--c-primary': rgb(v).join(' ') }),
        theme_hover_color:    (v) => ({ '--theme-hover': v }),
    };

    function preview(key, val) {
        if (!HEX.test(val)) return;
        if (adminVars[key]) {
            const map = adminVars[key](val);
            Object.keys(map).forEach(k => root.style.setProperty(k, map[k]));
        }
    }

    function setFieldVal(name, val) {
        const text = document.querySelector(`input[name="${name}"]`);
        if (text) {
            text.value = val.toUpperCase();
            const picker = text.parentElement.querySelector('input[type="color"]');
            if (picker) picker.value = val.toLowerCase();
            preview(name, val);
        }
    }

    window.applyAdminPreset = function (primaryBg, primaryHover, accentBg, sidebarBg, sidebarActive, themePrimary, themeHover) {
        setFieldVal('btn_primary_bg', primaryBg);
        setFieldVal('btn_primary_hover', primaryHover);
        setFieldVal('btn_accent_bg', accentBg);
        setFieldVal('sidebar_bg_color', sidebarBg);
        setFieldVal('sidebar_active_color', sidebarActive);
        setFieldVal('theme_primary_color', themePrimary);
        setFieldVal('theme_hover_color', themeHover);
        setFieldVal('btn_primary_text', '#FFFFFF');
        setFieldVal('btn_accent_text', '#FFFFFF');
        setFieldVal('sidebar_text_color', contrast(sidebarBg).toUpperCase());
        if (window.toastr) toastr.success('Admin preset applied. Save settings to make permanent.');
    };

    // Tab switching
    document.querySelectorAll('[data-target-tab]').forEach(function (tabBtn) {
        tabBtn.addEventListener('click', function () {
            document.querySelectorAll('[data-target-tab]').forEach(b => b.classList.remove('is-active'));
            document.querySelectorAll('.settings-tab').forEach(t => t.classList.add('hidden'));

            tabBtn.classList.add('is-active');
            const target = document.getElementById(tabBtn.getAttribute('data-target-tab'));
            if (target) target.classList.remove('hidden');
        });
    });

    // Color pickers synchronization
    document.querySelectorAll('#settingsForm input[type="color"]').forEach(function (picker) {
        const text = picker.parentElement.querySelector('input[type="text"]');
        if (!text) return;
        picker.addEventListener('input', function () {
            text.value = picker.value.toUpperCase();
            preview(text.name, picker.value);
        });
        text.addEventListener('input', function () {
            const v = text.value.trim();
            if (HEX.test(v)) {
                picker.value = v.toLowerCase();
                preview(text.name, v);
            }
        });
    });

    const storeName = document.getElementById('storeName');
    if (storeName) {
        storeName.addEventListener('input', function () {
            const v = storeName.value.trim() || 'Fresh Express';
            const pv = document.getElementById('pvStoreName');
            if (pv) pv.innerHTML = '<i class="ph-fill ph-basket"></i> ' + v;
            document.querySelectorAll('.sb-brand-name').forEach(el => { el.textContent = v; });
        });
    }
})();
</script>
@endpush
