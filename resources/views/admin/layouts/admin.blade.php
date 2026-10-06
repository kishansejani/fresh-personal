@php
    use App\Models\User;
    use App\Models\Role;
    use App\Models\Setting;

    $sysSettings = Setting::getAllSettings();
    $authUser    = Auth::user();
    $storeName   = $sysSettings['store_name'] ?? 'Admin Console';

    // ---- Colour helpers -----------------------------------------------------------------
    $hexToRgb = function ($hex, $fallback = '15 23 42') {
        $hex = ltrim(trim((string) $hex), '#');
        if (strlen($hex) === 3) { $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2]; }
        if (!preg_match('/^[0-9a-fA-F]{6}$/', $hex)) { return $fallback; }
        return hexdec(substr($hex, 0, 2)).' '.hexdec(substr($hex, 2, 2)).' '.hexdec(substr($hex, 4, 2));
    };
    $contrast = function ($hex) use ($hexToRgb) {
        [$r, $g, $b] = array_map('intval', explode(' ', $hexToRgb($hex)));
        return ((0.299 * $r + 0.587 * $g + 0.114 * $b) / 255) > 0.6 ? '#0f172a' : '#ffffff';
    };

    $primary       = $sysSettings['theme_primary_color'] ?? '#0f172a';
    $sidebarBg     = $sysSettings['sidebar_bg_color'] ?? '#000000';
    $sidebarActive = $sysSettings['sidebar_active_color'] ?? '#add8e6';
    $sidebarText   = $sysSettings['sidebar_text_color'] ?? $contrast($sidebarBg);

    $can = fn ($perm) => $authUser && ($perm === null || $authUser->hasPermission($perm));

    // ---- Sidebar navigation definition ---------------------------------------------------
    $navGroups = [
        ['label' => 'Overview', 'items' => [
            ['label' => 'Dashboard', 'icon' => 'squares-four', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard', 'perm' => null],
        ]],
        ['label' => 'Administration', 'items' => [
            ['label' => 'Users', 'icon' => 'users-three', 'route' => 'admin.users.index', 'active' => 'admin.users.*', 'perm' => 'manage_users'],
            ['label' => 'Roles & Permissions', 'icon' => 'shield-check', 'route' => 'admin.roles.index', 'active' => 'admin.roles.*', 'perm' => 'manage_roles'],
            ['label' => 'Settings', 'icon' => 'gear-six', 'route' => 'admin.settings.index', 'active' => 'admin.settings.*', 'perm' => 'manage_settings'],
        ]],
    ];

    $activeGroup = null; $activeItem = null;
    foreach ($navGroups as $gi => $group) {
        $navGroups[$gi]['key']   = \Illuminate\Support\Str::slug($group['label']);
        $navGroups[$gi]['label'] = __($group['label']);
        $group['items'] = array_map(fn ($i) => array_merge($i, ['label' => __($i['label'])]), $group['items']);
        $navGroups[$gi]['items'] = array_values(array_filter($group['items'], fn ($i) => $can($i['perm'])));
        foreach ($navGroups[$gi]['items'] as $ii => $item) {
            $isActive = request()->routeIs(...(array) $item['active']);
            $navGroups[$gi]['items'][$ii]['isActive'] = $isActive;
            if ($isActive) { $activeGroup = $group['label']; $activeItem = $item; }
        }
    }
    $navGroups = array_values(array_filter($navGroups, fn ($g) => count($g['items'])));

    // Quick actions for command palette
    $quickActions = array_values(array_filter([
        $can('manage_users')    ? ['label' => 'Add new user', 'icon' => 'user-plus', 'url' => route('admin.users.create'), 'hint' => 'Administration'] : null,
        $can('manage_roles')    ? ['label' => 'Create role', 'icon' => 'shield-plus', 'url' => route('admin.roles.create'), 'hint' => 'Administration'] : null,
        $can('manage_settings') ? ['label' => 'System settings', 'icon' => 'gear-six', 'url' => route('admin.settings.index'), 'hint' => 'Administration'] : null,
    ]));

    $paletteItems = [];
    foreach ($navGroups as $group) {
        foreach ($group['items'] as $item) {
            $paletteItems[] = ['label' => $item['label'], 'icon' => $item['icon'], 'url' => route($item['route']), 'hint' => $group['label']];
        }
    }
    $paletteItems = array_merge($paletteItems, $quickActions);

    $pageTitle = __(html_entity_decode(trim($__env->yieldContent('title', 'Admin Portal')), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    $btnPrimaryBg = $sysSettings['btn_primary_bg'] ?? '#0f172a';
    [$bpR, $bpG, $bpB] = array_map('intval', explode(' ', $hexToRgb($btnPrimaryBg)));
    $btnPrimaryIsDark = ((0.299 * $bpR + 0.587 * $bpG + 0.114 * $bpB) / 255) < 0.28;
    $initials  = collect(explode(' ', $authUser->name ?? 'Admin'))->filter()->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="{{ $sidebarBg }}">
    <title>{{ $pageTitle }} · {{ $storeName }}</title>

    <script>
        (function () {
            var d = document.documentElement, mode = 'system';
            try { mode = localStorage.getItem('admin_theme_mode') || '{{ $sysSettings['theme_mode'] ?? 'system' }}'; } catch (e) {}
            var dark = mode === 'dark' || (mode === 'system' && window.matchMedia && matchMedia('(prefers-color-scheme: dark)').matches);
            if (dark) d.classList.add('dark');
            try { if (localStorage.getItem('admin_sidebar_collapsed') === 'true') d.classList.add('sidebar-mini'); } catch (e) {}
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Hind+Vadodara:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.1/src/regular/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.1/src/fill/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.1/src/bold/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.1/src/duotone/style.css">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: 'rgb(var(--c-primary) / <alpha-value>)',
                        hover: 'var(--theme-hover)',
                        sidebarBg: 'var(--sidebar-bg)',
                        sidebarActive: 'var(--sidebar-active)',
                        brand: { 50: '#ecfdf5', 100: '#d1fae5', 200: '#a7f3d0', 300: '#6ee7b7', 400: '#34d399', 500: '#10b981', 600: '#059669', 700: '#047857', 800: '#065f46', 900: '#064e3b', 950: '#022c22' },
                    },
                    fontFamily: { sans: ['"Plus Jakarta Sans"', '"Hind Vadodara"', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
                    boxShadow: { soft: '0 1px 2px rgba(16,24,40,.04), 0 1px 3px rgba(16,24,40,.06)', lift: '0 12px 32px -12px rgba(15,23,42,.25)' },
                }
            }
        }
    </script>

    <style>
        :root {
            --theme-primary: {{ $primary }};
            --c-primary: {{ $hexToRgb($primary) }};
            --theme-primary-contrast: {{ $contrast($primary) }};
            --theme-hover: {{ $sysSettings['theme_hover_color'] ?? '#334155' }};
            --btn-primary-bg: {{ $sysSettings['btn_primary_bg'] ?? '#0f172a' }};
            --btn-primary-text: {{ $sysSettings['btn_primary_text'] ?? '#ffffff' }};
            --btn-primary-hover: {{ $sysSettings['btn_primary_hover'] ?? '#1e293b' }};
            --btn-accent-bg: {{ $sysSettings['btn_accent_bg'] ?? '#10b981' }};
            --btn-accent-text: {{ $sysSettings['btn_accent_text'] ?? '#ffffff' }};
            --sidebar-bg: {{ $sidebarBg }};
            --c-sidebar-text: {{ $hexToRgb($sidebarText, '255 255 255') }};
            --sidebar-text: {{ $sidebarText }};
            --sidebar-active: {{ $sidebarActive }};
            --sidebar-active-text: {{ $contrast($sidebarActive) }};
        }
        @if($btnPrimaryIsDark)
        .dark { --btn-primary-bg: #e2e8f0; --btn-primary-text: #0f172a; --btn-primary-hover: #ffffff; }
        @endif

        /* Floating Customizer Button Styles */
        .customizer-widget {
            position: fixed; bottom: 28px; right: 28px; z-index: 99999; touch-action: none; user-select: none;
        }
        .customizer-circle-btn {
            width: 54px; height: 54px; border-radius: 50%;
            background: linear-gradient(135deg, #10b981, #059669);
            color: #ffffff; display: flex; align-items: center; justify-content: center;
            font-size: 24px; box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.2) inset;
            cursor: pointer; border: 0; outline: none; transition: transform 0.2s, box-shadow 0.2s;
            position: relative; z-index: 2;
        }
        .customizer-circle-btn:hover {
            transform: scale(1.08) rotate(30deg); box-shadow: 0 14px 30px -5px rgba(16, 185, 129, 0.65);
        }
        .customizer-radar-wave {
            position: absolute; inset: -8px; border-radius: 50%;
            background: rgba(16, 185, 129, 0.35); pointer-events: none; z-index: 1;
            animation: radarPulse 2.4s cubic-bezier(0.2, 0.8, 0.4, 1) infinite;
        }
        .customizer-radar-wave.wave-2 { animation-delay: 0.8s; }
        .customizer-radar-wave.wave-3 { animation-delay: 1.6s; }
        @keyframes radarPulse {
            0% { transform: scale(0.9); opacity: 0.8; }
            100% { transform: scale(1.9); opacity: 0; }
        }

        /* Customizer Drawer Modal */
        .customizer-backdrop {
            position: fixed; inset: 0; z-index: 100000; background: rgba(15, 23, 42, 0.55);
            backdrop-filter: blur(4px); display: flex; justify-content: flex-end; opacity: 1;
            transition: opacity 0.25s ease;
        }
        .customizer-backdrop.is-hidden { opacity: 0; pointer-events: none; }
        .customizer-panel {
            width: 100%; max-width: 380px; height: 100%; background: var(--surface, #ffffff);
            color: var(--ink, #0f172a); display: flex; flex-direction: column;
            box-shadow: -10px 0 35px rgba(0, 0, 0, 0.2); transform: translateX(0);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .customizer-backdrop.is-hidden .customizer-panel { transform: translateX(100%); }
    </style>
    <link rel="stylesheet" href="{{ asset('assets/shared/fx-select.css') }}?v=3.1.0">
    <link rel="stylesheet" href="{{ asset('assets/admin/admin.css') }}?v=2.1.0">
    
    {{-- DataTables Styles --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    
    @stack('styles')
</head>
<body class="admin-body">
    <div id="pageProgress" class="page-progress"></div>
    <div id="toastStack" class="toast-stack" aria-live="polite" aria-atomic="true"></div>

    {{-- ============================== SIDEBAR ============================== --}}
    <aside id="adminSidebar" class="app-sidebar" aria-label="Main navigation">
        <div class="sb-brand">
            <a href="{{ route('admin.dashboard') }}" class="sb-brand-link">
                <span class="sb-logo"><i class="ph-fill ph-basket"></i></span>
                <span class="sb-brand-text">
                    <span class="sb-brand-name">{{ $storeName }}</span>
                    <span class="sb-brand-sub">{{ __('Admin Console') }}</span>
                </span>
            </a>
            <button type="button" class="sb-close" data-sidebar-toggle aria-label="Close menu"><i class="ph ph-x"></i></button>
        </div>

        <div class="sb-search">
            <i class="ph ph-magnifying-glass"></i>
            <input type="search" id="sidebarFilter" placeholder="{{ __('Filter menu…') }}" autocomplete="off" aria-label="Filter menu">
        </div>

        <nav class="sb-nav" id="sidebarNav">
            @foreach($navGroups as $group)
                @php $gKey = $group['key']; @endphp
                <div class="sb-group" data-group="{{ $gKey }}">
                    <button type="button" class="sb-group-title" data-group-toggle="{{ $gKey }}">
                        <span>{{ $group['label'] }}</span>
                        <i class="ph ph-caret-down"></i>
                    </button>
                    <ul class="sb-group-items">
                        @foreach($group['items'] as $item)
                            <li>
                                <a href="{{ route($item['route']) }}"
                                   class="sb-item {{ $item['isActive'] ? 'is-active' : '' }}"
                                   data-sb-link>
                                    <span class="sb-icon"><i class="ph-duotone ph-{{ $item['icon'] }}"></i></span>
                                    <span class="sb-label">{{ $item['label'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </nav>

        {{-- Footer user profile badge --}}
        <div class="sb-footer">
            <div class="sb-user">
                <span class="sb-avatar">{{ $initials }}</span>
                <div class="sb-user-meta">
                    <span class="sb-user-name">{{ $authUser->name ?? 'Admin User' }}</span>
                    <span class="sb-user-role">{{ $authUser->roleModel->display_name ?? ucfirst($authUser->role ?? 'Staff') }}</span>
                </div>
                <form action="{{ route('admin.logout') }}" method="POST" class="sb-logout-form">
                    @csrf
                    <button type="submit" class="sb-logout" title="{{ __('Sign out') }}" aria-label="{{ __('Sign out') }}">
                        <i class="ph-bold ph-sign-out"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <div class="sidebar-backdrop" data-sidebar-toggle tabindex="-1" aria-hidden="true"></div>

    {{-- ============================== MAIN CONTENT WRAPPER ============================== --}}
    <div class="app-main">

        {{-- Top App Header (Matching Image 1 & 2) --}}
        <header class="app-topbar">
            <div class="flex items-center gap-2">
                <button type="button" class="tb-btn" data-sidebar-toggle aria-label="Toggle sidebar" title="Toggle sidebar">
                    <i class="ph ph-sidebar text-xl"></i>
                </button>

                <nav class="tb-breadcrumb hidden sm:flex items-center" aria-label="Breadcrumb">
                    <a href="{{ route('admin.dashboard') }}" class="tb-crumb" title="Dashboard">
                        <i class="ph-duotone ph-squares-four text-base"></i>
                    </a>
                    @if($activeGroup && $activeGroup !== 'Overview')
                        <i class="ph ph-caret-right tb-crumb-sep"></i>
                        <span class="tb-crumb">{{ $activeGroup }}</span>
                    @endif
                    @if($activeItem)
                        <i class="ph ph-caret-right tb-crumb-sep"></i>
                        <span class="tb-crumb is-current">{{ $activeItem['label'] }}</span>
                    @endif
                </nav>
            </div>

            <div class="flex items-center gap-2.5">
                {{-- Search Bar --}}
                <button type="button" class="tb-search" data-palette-open aria-label="Search">
                    <i class="ph ph-magnifying-glass"></i>
                    <span class="hidden sm:inline">{{ __('Search or jump to…') }}</span>
                    <kbd class="hidden lg:inline-flex">Ctrl K</kbd>
                </button>

                {{-- Fullscreen Monitor Toggle (Monitor icon + Expand icon) --}}
                <button type="button" class="tb-btn" id="fullscreenTopBtn" title="{{ __('Toggle Fullscreen') }}" aria-label="{{ __('Fullscreen') }}">
                    <i class="ph ph-desktop text-lg"></i>
                </button>
                <button type="button" class="tb-btn hidden sm:inline-flex" id="expandTopBtn" title="{{ __('Expand') }}">
                    <i class="ph ph-corners-out text-lg"></i>
                </button>

                {{-- Language Switcher Dropdown (文A) --}}
                <div class="relative" data-dropdown>
                    <button type="button" class="tb-btn tb-btn-text" data-dropdown-trigger aria-expanded="false" title="{{ __('Switch language') }}">
                        <i class="ph ph-translate text-lg"></i>
                        <span class="text-xs font-extrabold uppercase">{{ app()->getLocale() }}</span>
                        <i class="ph ph-caret-down text-xs"></i>
                    </button>
                    <div class="tb-menu w-44" data-dropdown-menu>
                        <div class="tb-menu-title">{{ __('Language') }}</div>
                        <a href="{{ route('lang.switch', 'en') }}" class="tb-menu-item {{ app()->getLocale() === 'en' ? 'is-selected' : '' }}">
                            <span>English (EN)</span>
                            <i class="ph-bold ph-check tm-check ml-auto"></i>
                        </a>
                        <a href="{{ route('lang.switch', 'gu') }}" class="tb-menu-item {{ app()->getLocale() === 'gu' ? 'is-selected' : '' }}">
                            <span>ગુજરાતી (GU)</span>
                            <i class="ph-bold ph-check tm-check ml-auto"></i>
                        </a>
                    </div>
                </div>

                {{-- Notification Bell Dropdown --}}
                <div class="relative" data-dropdown>
                    <button type="button" class="tb-btn relative" id="adminNotifBellBtn" data-dropdown-trigger aria-expanded="false" title="{{ __('Notifications') }}">
                        <i class="ph ph-bell text-lg"></i>
                        <span id="notifBadgeCount" class="tb-dot hidden"></span>
                    </button>
                    <div class="tb-menu tb-menu-wide" data-dropdown-menu>
                        <div class="flex items-center justify-between p-3 border-b border-slate-100 dark:border-slate-800">
                            <span class="font-extrabold text-sm text-slate-900 dark:text-white">{{ __('Notifications') }}</span>
                            <span class="text-xs text-slate-400" id="notifHeaderCount">{{ __('System alerts') }}</span>
                        </div>
                        <div class="p-2 max-h-80 overflow-y-auto space-y-1">
                            <div class="notif-item">
                                <span class="notif-icon tone-emerald"><i class="ph-duotone ph-check-circle"></i></span>
                                <div>
                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-100">{{ __('System Active') }}</p>
                                    <p class="text-[11px] text-slate-400">{{ __('Administration portal operational and secure.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- User Profile Dropdown --}}
                <div class="relative" data-dropdown>
                    <button type="button" class="tb-profile" data-dropdown-trigger aria-expanded="false">
                        <span class="tb-avatar">{{ $initials }}</span>
                        <div class="hidden xl:block text-left leading-tight">
                            <span class="block text-xs font-bold text-slate-900 dark:text-white truncate max-w-[120px]">{{ $authUser->name ?? 'Admin' }}</span>
                            <span class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider">{{ $authUser->roleModel->display_name ?? 'SUPER ADMIN' }}</span>
                        </div>
                        <i class="ph ph-caret-down text-xs text-slate-400"></i>
                    </button>
                    <div class="tb-menu w-56" data-dropdown-menu>
                        <div class="p-3 border-b border-slate-100 dark:border-slate-800">
                            <p class="text-xs font-bold text-slate-900 dark:text-white">{{ $authUser->name }}</p>
                            <p class="text-[11px] text-slate-400 truncate">{{ $authUser->email ?? $authUser->phone }}</p>
                        </div>
                        <a href="{{ route('admin.users.edit', $authUser) }}" class="tb-menu-item mt-1"><i class="ph ph-user-circle"></i> {{ __('Edit Profile') }}</a>
                        @if($can('manage_settings'))
                            <a href="{{ route('admin.settings.index') }}" class="tb-menu-item"><i class="ph ph-gear-six"></i> {{ __('Settings') }}</a>
                        @endif
                        <form action="{{ route('admin.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="tb-menu-item text-rose-600 dark:text-rose-400 hover:!bg-rose-50 dark:hover:!bg-rose-500/10">
                                <i class="ph ph-sign-out"></i> {{ __('Sign out') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- Main Page Body --}}
        <main class="app-content" id="mainContent">
            @if(session('success'))
                <div class="mb-5 flex items-center justify-between p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 text-emerald-800 dark:text-emerald-200 text-sm font-semibold shadow-xs" role="status">
                    <div class="flex items-center gap-2.5">
                        <i class="ph-fill ph-check-circle text-emerald-500 text-lg shrink-0"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 dark:hover:text-emerald-300 p-1" aria-label="Close"><i class="ph ph-x"></i></button>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-5 flex items-center justify-between p-4 rounded-2xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 text-rose-800 dark:text-rose-200 text-sm font-semibold shadow-xs" role="alert">
                    <div class="flex items-center gap-2.5">
                        <i class="ph-fill ph-warning-circle text-rose-500 text-lg shrink-0"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-800 dark:hover:text-rose-300 p-1" aria-label="Close"><i class="ph ph-x"></i></button>
                </div>
            @endif

            @yield('content')
        </main>

        {{-- Footer --}}
        <footer class="app-footer">
            <p>{{ $sysSettings['footer_copyright_prefix'] ?? ('© ' . date('Y') . ', made with ❤️ by') }}
                <a href="{{ $sysSettings['footer_creator_url'] ?? 'https://decentinfoways.com' }}" target="_blank" rel="noopener" class="font-bold text-slate-700 dark:text-slate-200 hover:underline">
                    {{ $sysSettings['footer_creator_name'] ?? 'Decent Infoways' }}
                </a>
            </p>
            <p class="text-slate-400 text-xs">{{ $storeName }} v1.0.0</p>
        </footer>
    </div>

    {{-- ============================== FLOATING CUSTOMIZER GEAR WIDGET & DRAWER ============================== --}}
    <div id="customizerWidget" class="customizer-widget">
        <div class="customizer-radar-wave wave-1"></div>
        <div class="customizer-radar-wave wave-2"></div>
        <div class="customizer-radar-wave wave-3"></div>

        <button type="button" id="customizerBtn" class="customizer-circle-btn" aria-label="Open Customizer" title="Customize Theme & Colors">
            <i class="ph-duotone ph-gear-six"></i>
        </button>
    </div>

    <!-- Customizer Drawer Modal -->
    <div id="customizerDrawer" class="customizer-backdrop is-hidden" aria-hidden="true">
        <div class="customizer-panel" role="dialog" aria-modal="true" aria-label="Customizer">
            <div class="flex items-center justify-between p-5 border-b border-slate-200 dark:border-slate-800">
                <div class="flex items-center gap-2.5 font-black text-slate-800 dark:text-white text-base">
                    <i class="ph-duotone ph-gear-six text-2xl text-emerald-600 dark:text-emerald-400"></i>
                    <span class="text-base font-extrabold tracking-tight">{{ __('Live Customizer') }}</span>
                </div>
                <button type="button" id="customizerClose" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-700 dark:hover:text-white" aria-label="Close"><i class="ph ph-x text-lg"></i></button>
            </div>

            <div class="flex-1 overflow-y-auto p-5 space-y-6">
                <!-- Theme Mode -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">{{ __('Theme Mode') }}</h4>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" class="p-3 rounded-xl border border-slate-200 dark:border-slate-800 flex flex-col items-center gap-1.5 text-xs font-bold hover:border-emerald-500 transition" data-cust-theme="light">
                            <i class="ph ph-sun text-lg text-amber-500"></i> {{ __('Light') }}
                        </button>
                        <button type="button" class="p-3 rounded-xl border border-slate-200 dark:border-slate-800 flex flex-col items-center gap-1.5 text-xs font-bold hover:border-emerald-500 transition" data-cust-theme="dark">
                            <i class="ph ph-moon-stars text-lg text-indigo-400"></i> {{ __('Dark') }}
                        </button>
                        <button type="button" class="p-3 rounded-xl border border-slate-200 dark:border-slate-800 flex flex-col items-center gap-1.5 text-xs font-bold hover:border-emerald-500 transition" data-cust-theme="system">
                            <i class="ph ph-desktop text-lg text-slate-400"></i> {{ __('System') }}
                        </button>
                    </div>
                </div>

                <!-- Full Settings Link -->
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
                    <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 mb-1">{{ __('Full Color Management') }}</h4>
                    <p class="text-[11.5px] text-slate-400 mb-3">{{ __('Configure detailed button, sidebar, brand colors & light/dark palettes.') }}</p>
                    <a href="{{ route('admin.settings.index') }}" class="btn btn-primary w-full justify-center text-xs">
                        <i class="ph ph-palette"></i> {{ __('Open Settings & Color Management') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================== COMMAND PALETTE MODAL ============================== --}}
    <div id="commandPalette" class="palette-modal hidden" role="dialog" aria-modal="true" aria-label="Command palette">
        <div class="palette-dialog">
            <div class="palette-input-wrap">
                <i class="ph ph-magnifying-glass text-lg text-slate-400"></i>
                <input type="search" id="paletteInput" placeholder="{{ __('Search pages, settings, users…') }}" autocomplete="off">
                <kbd class="palette-esc-kbd">ESC</kbd>
            </div>
            <div class="palette-list" id="paletteList">
                @foreach($paletteItems as $p)
                    <a href="{{ $p['url'] }}" class="palette-item" data-palette-item>
                        <i class="ph-duotone ph-{{ $p['icon'] }}"></i>
                        <span class="palette-item-label">{{ $p['label'] }}</span>
                        <span class="palette-item-hint">{{ $p['hint'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script>
        window.__FX_PALETTE__ = @json($paletteItems);
    </script>
    <script src="{{ asset('assets/admin/admin.js') }}?v=2.1.0"></script>
    <script src="{{ asset('assets/shared/fx-select.js') }}?v=3.1.0"></script>
    <script>
        // Floating Gear Customizer drawer interactions
        (function() {
            var btn = document.getElementById('customizerBtn');
            var drawer = document.getElementById('customizerDrawer');
            var closeBtn = document.getElementById('customizerClose');

            if (btn && drawer) {
                btn.addEventListener('click', function() {
                    drawer.classList.remove('is-hidden');
                });
            }
            if (closeBtn && drawer) {
                closeBtn.addEventListener('click', function() {
                    drawer.classList.add('is-hidden');
                });
            }
            if (drawer) {
                drawer.addEventListener('click', function(e) {
                    if (e.target === drawer) drawer.classList.add('is-hidden');
                });
            }

            // Theme toggle from drawer
            document.querySelectorAll('[data-cust-theme]').forEach(function(b) {
                b.addEventListener('click', function() {
                    var m = b.getAttribute('data-cust-theme');
                    if (window.AdminTheme && window.AdminTheme.set) {
                        window.AdminTheme.set(m);
                    } else {
                        localStorage.setItem('admin_theme_mode', m);
                        var dark = m === 'dark' || (m === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
                        document.documentElement.classList.toggle('dark', dark);
                    }
                });
            });

            // Fullscreen topbar buttons
            var fsBtns = [document.getElementById('fullscreenTopBtn'), document.getElementById('expandTopBtn')];
            fsBtns.forEach(function(fsBtn) {
                if (fsBtn) {
                    fsBtn.addEventListener('click', function() {
                        if (!document.fullscreenElement) {
                            document.documentElement.requestFullscreen().catch(function(){});
                        } else {
                            document.exitFullscreen().catch(function(){});
                        }
                    });
                }
            });
        })();
    </script>
    @stack('scripts')
</body>
</html>
