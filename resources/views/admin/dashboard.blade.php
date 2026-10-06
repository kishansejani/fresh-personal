@extends('admin.layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Welcome Banner --}}
    <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white relative overflow-hidden shadow-xl border border-slate-700/50">
        <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full bg-emerald-500/20 blur-3xl pointer-events-none"></div>
        <div class="absolute right-32 -bottom-16 w-48 h-48 rounded-full bg-blue-500/15 blur-3xl pointer-events-none"></div>

        <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 text-[11px] font-bold uppercase tracking-wider text-emerald-300">
                    <i class="ph-fill ph-check-circle"></i> {{ __('System Active & Operational') }}
                </span>
                <h1 class="mt-3 text-2xl sm:text-3xl font-extrabold tracking-tight">
                    {{ __('Welcome back') }}, {{ Auth::user()->name }}! 👋
                </h1>
                <p class="mt-1 text-sm text-slate-300 max-w-xl">
                    {{ __('Administration Console — manage users, configure granular roles and customize system settings seamlessly.') }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.users.create') }}" class="btn bg-emerald-600 hover:bg-emerald-500 text-white font-bold !border-0 shadow-lg shadow-emerald-600/25">
                    <i class="ph-bold ph-user-plus text-lg"></i> {{ __('Add User') }}
                </a>
                <a href="{{ route('admin.roles.create') }}" class="btn bg-white/10 hover:bg-white/20 text-white font-semibold backdrop-blur-sm !border-white/10">
                    <i class="ph-bold ph-shield-plus text-lg"></i> {{ __('Add Role') }}
                </a>
            </div>
        </div>
    </div>

    {{-- Top Statistics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">

        {{-- Card 1: Total Users --}}
        <div class="card p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">{{ __('Total Users') }}</span>
                    <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white mt-1">{{ number_format($totalUsers) }}</h3>
                </div>
                <span class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center text-2xl">
                    <i class="ph-duotone ph-users-three"></i>
                </span>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                <span class="text-emerald-600 dark:text-emerald-400 font-semibold flex items-center gap-1">
                    <i class="ph-fill ph-circle text-[8px]"></i> {{ $activeUsers }} {{ __('Active') }}
                </span>
                <span class="text-slate-400 font-medium">
                    {{ $inactiveUsers }} {{ __('Inactive') }}
                </span>
            </div>
        </div>

        {{-- Card 2: System Roles --}}
        <div class="card p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">{{ __('Roles Configured') }}</span>
                    <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white mt-1">{{ number_format($totalRoles) }}</h3>
                </div>
                <span class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-2xl">
                    <i class="ph-duotone ph-shield-check"></i>
                </span>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                <span class="text-slate-600 dark:text-slate-300 font-semibold">
                    {{ $superAdminsCount }} {{ __('Super Admins') }}
                </span>
                <span class="text-slate-400 font-medium">
                    {{ $adminsCount }} {{ __('Admins') }}
                </span>
            </div>
        </div>

        {{-- Card 3: Permissions --}}
        <div class="card p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">{{ __('Permissions') }}</span>
                    <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white mt-1">{{ number_format($totalPermissions) }}</h3>
                </div>
                <span class="w-12 h-12 rounded-2xl bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center text-2xl">
                    <i class="ph-duotone ph-key"></i>
                </span>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                <span class="text-purple-600 dark:text-purple-400 font-semibold">
                    {{ __('Granular access control') }}
                </span>
                <span class="text-slate-400">
                    {{ __('Role Matrix') }}
                </span>
            </div>
        </div>

        {{-- Card 4: Settings & Mode --}}
        <div class="card p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">{{ __('Theme & Brand') }}</span>
                    <h3 class="text-xl font-extrabold text-slate-900 dark:text-white mt-1 truncate max-w-[130px]">{{ $settings['store_name'] ?? 'Console' }}</h3>
                </div>
                <span class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center text-2xl">
                    <i class="ph-duotone ph-gear-six"></i>
                </span>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                <span class="text-amber-600 dark:text-amber-400 font-semibold uppercase">
                    {{ $settings['theme_mode'] ?? 'System' }} {{ __('Mode') }}
                </span>
                <a href="{{ route('admin.settings.index') }}" class="text-slate-500 hover:text-slate-900 dark:hover:text-white font-medium hover:underline">
                    {{ __('Customize') }} &rarr;
                </a>
            </div>
        </div>

    </div>

    {{-- Main Grid: Recent Users & Module Quick Access --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left 2 cols: Recent Users Table --}}
        <div class="lg:col-span-2 card overflow-hidden">
            <div class="card-header flex items-center justify-between p-5 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h3 class="card-title text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="ph-duotone ph-users-three text-primary"></i> {{ __('Recent Users') }}
                    </h3>
                    <p class="card-subtitle text-xs text-slate-500">{{ __('Latest registered and active system accounts') }}</p>
                </div>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline btn-sm">
                    {{ __('View all users') }} &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 text-xs uppercase font-semibold">
                        <tr>
                            <th class="py-3 px-4">{{ __('User') }}</th>
                            <th class="py-3 px-4">{{ __('Role') }}</th>
                            <th class="py-3 px-4">{{ __('Status') }}</th>
                            <th class="py-3 px-4">{{ __('Joined') }}</th>
                            <th class="py-3 px-4 text-right">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                        @forelse($recentUsers as $user)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center font-bold text-xs text-slate-700 dark:text-slate-200">
                                            {{ mb_substr($user->name ?? 'U', 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-900 dark:text-white text-xs sm:text-sm">{{ $user->name }}</p>
                                            <p class="text-[11px] text-slate-400">{{ $user->email ?? $user->phone }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $user->role === 'super_admin' ? 'bg-purple-100 text-purple-800 dark:bg-purple-500/20 dark:text-purple-300' : ($user->role === 'admin' ? 'bg-blue-100 text-blue-800 dark:bg-blue-500/20 dark:text-blue-300' : 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300') }}">
                                        {{ $user->roleModel->display_name ?? ucfirst($user->role ?? 'User') }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    @if($user->is_active)
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                                            <i class="ph-fill ph-check-circle"></i> {{ __('Active') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-rose-600 dark:text-rose-400">
                                            <i class="ph-fill ph-x-circle"></i> {{ __('Inactive') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-xs text-slate-400">
                                    {{ $user->created_at ? $user->created_at->format('d M Y') : '—' }}
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-ghost btn-sm !p-1.5" title="{{ __('Edit') }}">
                                        <i class="ph ph-pencil-simple text-base"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-slate-400">
                                    {{ __('No users found.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Right 1 col: Quick Modules & System Status --}}
        <div class="space-y-6">

            {{-- Administration Modules Card --}}
            <div class="card p-5">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-3 flex items-center gap-2">
                    <i class="ph-duotone ph-squares-four text-primary"></i> {{ __('Administration Modules') }}
                </h3>
                <div class="space-y-2.5">
                    <a href="{{ route('admin.users.index') }}" class="flex items-center justify-between p-3 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-blue-400 dark:hover:border-blue-500/50 hover:bg-blue-50/30 dark:hover:bg-blue-500/5 transition group">
                        <div class="flex items-center gap-3">
                            <span class="w-9 h-9 rounded-xl bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center text-lg">
                                <i class="ph-duotone ph-users-three"></i>
                            </span>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white group-hover:text-blue-600 transition">{{ __('Users') }}</h4>
                                <p class="text-[11px] text-slate-400">{{ __('Create, edit & manage users') }}</p>
                            </div>
                        </div>
                        <i class="ph ph-caret-right text-slate-400 group-hover:translate-x-0.5 transition-transform"></i>
                    </a>

                    <a href="{{ route('admin.roles.index') }}" class="flex items-center justify-between p-3 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-emerald-400 dark:hover:border-emerald-500/50 hover:bg-emerald-50/30 dark:hover:bg-emerald-500/5 transition group">
                        <div class="flex items-center gap-3">
                            <span class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg">
                                <i class="ph-duotone ph-shield-check"></i>
                            </span>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white group-hover:text-emerald-600 transition">{{ __('Roles & Permissions') }}</h4>
                                <p class="text-[11px] text-slate-400">{{ __('Role matrix & permission grants') }}</p>
                            </div>
                        </div>
                        <i class="ph ph-caret-right text-slate-400 group-hover:translate-x-0.5 transition-transform"></i>
                    </a>

                    <a href="{{ route('admin.settings.index') }}" class="flex items-center justify-between p-3 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-purple-400 dark:hover:border-purple-500/50 hover:bg-purple-50/30 dark:hover:bg-purple-500/5 transition group">
                        <div class="flex items-center gap-3">
                            <span class="w-9 h-9 rounded-xl bg-purple-100 dark:bg-purple-500/20 text-purple-600 dark:text-purple-400 flex items-center justify-center text-lg">
                                <i class="ph-duotone ph-gear-six"></i>
                            </span>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white group-hover:text-purple-600 transition">{{ __('Settings') }}</h4>
                                <p class="text-[11px] text-slate-400">{{ __('Panel colors, sidebar & footer') }}</p>
                            </div>
                        </div>
                        <i class="ph ph-caret-right text-slate-400 group-hover:translate-x-0.5 transition-transform"></i>
                    </a>
                </div>
            </div>

            {{-- System Information Card --}}
            <div class="card p-5">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-3 flex items-center gap-2">
                    <i class="ph-duotone ph-cpu text-primary"></i> {{ __('System Environment') }}
                </h3>
                <dl class="space-y-2 text-xs">
                    <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                        <dt class="text-slate-400">{{ __('PHP Version') }}</dt>
                        <dd class="font-mono font-bold text-slate-700 dark:text-slate-200">{{ $systemInfo['php_version'] }}</dd>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                        <dt class="text-slate-400">{{ __('Laravel Version') }}</dt>
                        <dd class="font-mono font-bold text-slate-700 dark:text-slate-200">v{{ $systemInfo['laravel_version'] }}</dd>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                        <dt class="text-slate-400">{{ __('Environment') }}</dt>
                        <dd class="font-semibold text-emerald-600 uppercase">{{ $systemInfo['server_environment'] }}</dd>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                        <dt class="text-slate-400">{{ __('Database') }}</dt>
                        <dd class="font-mono font-semibold text-slate-700 dark:text-slate-200">{{ $systemInfo['db_name'] }}</dd>
                    </div>
                    <div class="flex justify-between py-1">
                        <dt class="text-slate-400">{{ __('Timezone') }}</dt>
                        <dd class="font-semibold text-slate-700 dark:text-slate-200">{{ $systemInfo['timezone'] }}</dd>
                    </div>
                </dl>
            </div>

        </div>

    </div>

</div>
@endsection
