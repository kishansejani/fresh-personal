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
                <span class="sb-logo"><i class="ph-fill ph-shield-check"></i></span>
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

        {{-- Top App Header --}}
        <header class="app-topbar">
            <div class="flex items-center gap-2">
                <button type="button" class="tb-btn lg:hidden" data-sidebar-toggle aria-label="Toggle menu">
                    <i class="ph-bold ph-list text-lg"></i>
                </button>
                <button type="button" class="tb-btn hidden lg:inline-flex" data-sidebar-toggle title="Toggle sidebar">
                    <i class="ph-bold ph-sidebar text-lg"></i>
                </button>

                <nav class="tb-breadcrumb flex items-center" aria-label="Breadcrumb">
                    <a href="{{ route('admin.dashboard') }}" class="tb-crumb" title="Dashboard">
                        <i class="ph-duotone ph-squares-four"></i>
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

            <div class="flex items-center gap-2">
                <button type="button" class="tb-search" data-palette-open aria-label="Search">
                    <i class="ph ph-magnifying-glass"></i>
                    <span class="hidden sm:inline">{{ __('Search…') }}</span>
                    <kbd class="hidden lg:inline-flex">Ctrl K</kbd>
                </button>

                {{-- Theme Switcher --}}
                <div class="relative" data-dropdown>
                    <button type="button" class="tb-btn" data-dropdown-trigger aria-expanded="false" title="{{ __('Switch theme') }}">
                        <i class="ph ph-sun text-lg" id="themeCurrentIcon"></i>
                    </button>
                    <div class="tb-menu w-44" data-dropdown-menu>
                        <div class="tb-menu-title">{{ __('Theme') }}</div>
                        <button type="button" class="tb-menu-item" data-theme-set="light"><i class="ph ph-sun"></i> {{ __('Light') }}<i class="ph-bold ph-check tm-check ml-auto"></i></button>
                        <button type="button" class="tb-menu-item" data-theme-set="dark"><i class="ph ph-moon-stars"></i> {{ __('Dark') }}<i class="ph-bold ph-check tm-check ml-auto"></i></button>
                        <button type="button" class="tb-menu-item" data-theme-set="system"><i class="ph ph-desktop"></i> {{ __('System') }}<i class="ph-bold ph-check tm-check ml-auto"></i></button>
                    </div>
                </div>

                {{-- Fullscreen toggle --}}
                <button type="button" class="tb-btn hidden sm:inline-flex" data-fullscreen title="{{ __('Fullscreen') }}">
                    <i class="ph ph-corners-out text-lg"></i>
                </button>

                {{-- User profile dropdown menu --}}
                <div class="relative" data-dropdown>
                    <button type="button" class="tb-profile" data-dropdown-trigger aria-expanded="false">
                        <span class="tb-avatar">{{ $initials }}</span>
                        <span class="hidden xl:block text-left leading-tight">
                            <span class="block text-xs font-bold text-slate-900 dark:text-white truncate max-w-[120px]">{{ $authUser->name ?? 'Admin' }}</span>
                            <span class="block text-[10.5px] font-semibold text-slate-400">{{ $authUser->roleModel->display_name ?? ucfirst($authUser->role ?? 'Staff') }}</span>
                        </span>
                        <i class="ph ph-caret-down text-xs text-slate-400"></i>
                    </button>
                    <div class="tb-menu w-56" data-dropdown-menu>
                        <div class="p-2.5 border-b border-slate-100 dark:border-slate-800">
                            <p class="text-xs font-bold text-slate-900 dark:text-white">{{ $authUser->name }}</p>
                            <p class="text-[11px] text-slate-400 truncate">{{ $authUser->email ?? $authUser->phone }}</p>
                        </div>
                        @if($can('manage_settings'))
                            <a href="{{ route('admin.settings.index') }}" class="tb-menu-item mt-1"><i class="ph ph-gear-six"></i> {{ __('Settings') }}</a>
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
    @stack('scripts')
</body>
</html>
