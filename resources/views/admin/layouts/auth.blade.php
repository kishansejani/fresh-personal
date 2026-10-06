<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $sysSettings = \App\Models\Setting::getAllSettings();
        $storeName = $sysSettings['store_name'] ?? 'Admin Console';
        $primary = $sysSettings['theme_primary_color'] ?? '#0f172a';
        $btnPrimaryBg = $sysSettings['btn_primary_bg'] ?? '#0f172a';
        $btnPrimaryText = $sysSettings['btn_primary_text'] ?? '#ffffff';
        $btnAccentBg = $sysSettings['btn_accent_bg'] ?? '#10b981';
    @endphp
    <title>@yield('title', 'Admin Sign In') - {{ $storeName }}</title>

    <script>
        (function () {
            var m = 'system';
            try { m = localStorage.getItem('admin_theme_mode') || '{{ $sysSettings['theme_mode'] ?? 'system' }}'; } catch (e) {}
            if (m === 'dark' || (m === 'system' && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Hind+Vadodara:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.1/src/regular/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.1/src/fill/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.1/src/bold/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.1/src/duotone/style.css">

    <link rel="stylesheet" href="{{ asset('assets/shared/fx-select.css') }}?v=3.1.0">
    <link rel="stylesheet" href="{{ asset('assets/admin/admin.css') }}?v=2.1.0">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '{{ $primary }}',
                        brand: { 50: '#ecfdf5', 100: '#d1fae5', 200: '#a7f3d0', 300: '#6ee7b7', 400: '#34d399', 500: '#10b981', 600: '#059669', 700: '#047857', 800: '#065f46', 900: '#064e3b', 950: '#022c22' },
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', '"Hind Vadodara"', 'system-ui', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        .fx-label { display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px; }
        .dark .fx-label { color: #cbd5e1; }
        .fx-input { width: 100%; border-radius: 12px; border: 1px solid #cbd5e1; padding: 10px 14px; font-size: 14px; background: #fff; color: #0f172a; transition: all .15s; outline: none; }
        .dark .fx-input { background: #0f172a; border-color: #334155; color: #f8fafc; }
        .fx-input:focus { border-color: #10b981; box-shadow: 0 0 0 3px rgba(16,185,129,.15); }
    </style>
    @stack('styles')
</head>
<body class="h-full antialiased bg-slate-50 dark:bg-slate-950">
<div class="min-h-full grid lg:grid-cols-[minmax(0,1.05fr)_minmax(0,1fr)]">

    <!-- Brand / info panel -->
    <aside class="relative hidden lg:flex flex-col justify-between overflow-hidden p-10 xl:p-14 text-white bg-[radial-gradient(ellipse_at_top_left,_#1e293b_0%,_#0f172a_45%,_#020617_100%)]">
        <div class="absolute inset-0 opacity-[.07]" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 22px 22px;"></div>
        <div class="absolute -right-24 -bottom-24 w-[28rem] h-[28rem] rounded-full bg-emerald-500/20 blur-3xl"></div>

        <div class="relative inline-flex items-center gap-3">
            <span class="w-11 h-11 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-700 flex items-center justify-center text-2xl shadow-lg shadow-emerald-500/30"><i class="ph-fill ph-shield-check"></i></span>
            <span class="leading-tight">
                <span class="block font-extrabold text-lg tracking-tight">{{ $storeName }}</span>
                <span class="block text-[11px] font-bold uppercase tracking-[.16em] text-emerald-300">Admin Control Center</span>
            </span>
        </div>

        <div class="relative max-w-md">
            @yield('panel')
        </div>

        <p class="relative text-[12px] text-slate-400">&copy; {{ date('Y') }} {{ $storeName }} · All rights reserved.</p>
    </aside>

    <!-- Form side -->
    <main class="relative flex flex-col min-h-full px-4 sm:px-8 py-6 sm:py-10 bg-white dark:bg-slate-950">
        <div class="flex items-center justify-between">
            <div class="lg:hidden inline-flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-700 text-white flex items-center justify-center text-xl"><i class="ph-fill ph-shield-check"></i></span>
                <span class="font-extrabold text-slate-900 dark:text-white">{{ $storeName }}</span>
            </div>
            <div class="ml-auto flex items-center gap-1">
                <button type="button" id="authThemeBtn" class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition" aria-label="Toggle Theme" title="Toggle Theme"><i class="ph ph-moon-stars text-lg"></i></button>
            </div>
        </div>

        <div class="flex-1 flex items-center justify-center py-8">
            <div class="w-full max-w-[26rem]">
                @yield('content')
            </div>
        </div>
    </main>
</div>

<script>
    // Password show / hide
    document.addEventListener('click', function (e) {
        var b = e.target.closest('[data-toggle-password]');
        if (!b) return;
        var input = document.getElementById(b.getAttribute('data-toggle-password'));
        var show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        b.querySelector('i').className = 'ph ' + (show ? 'ph-eye-slash' : 'ph-eye') + ' text-lg';
        b.setAttribute('aria-pressed', show ? 'true' : 'false');
    });

    // Theme toggle
    (function () {
        var btn = document.getElementById('authThemeBtn');
        if (!btn) return;
        function sync() { btn.querySelector('i').className = 'ph ' + (document.documentElement.classList.contains('dark') ? 'ph-sun' : 'ph-moon-stars') + ' text-lg'; }
        btn.addEventListener('click', function () {
            var dark = !document.documentElement.classList.contains('dark');
            document.documentElement.classList.toggle('dark', dark);
            try { localStorage.setItem('admin_theme_mode', dark ? 'dark' : 'light'); } catch (e) {}
            sync();
        });
        sync();
    })();
</script>
@stack('scripts')
</body>
</html>
