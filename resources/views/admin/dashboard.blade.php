@extends('admin.layouts.admin')

@section('title', 'Dashboard')

@section('content')
@php
    $hour = (int) date('H');
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
@endphp

<div class="space-y-6">

    {{-- ============================== TOP WELCOME & LIVE CLOCK CARD ============================== --}}
    <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    {{ $greeting }}, {{ Auth::user()->name }}
                </h1>
                <p class="mt-1.5 text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium">
                    {{ __('Administration & System Operations Console') }}
                </p>
            </div>

            {{-- Right-aligned Live Digital Clock & Date --}}
            <div class="text-left md:text-right">
                <span class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500" id="liveDateStr">
                    {{ strtoupper(date('l, d-m-Y')) }}
                </span>
                <span class="block text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white font-mono tracking-tight" id="liveTimeClock">
                    {{ date('h:i:s A') }}
                </span>
            </div>
        </div>
    </div>

    {{-- ============================== DASHBOARD CUSTOMIZER TOOLBAR ============================== --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                <i class="ph-duotone ph-squares-four text-base text-emerald-500"></i>
                <span id="activeWidgetCount">8</span> {{ __('widgets active') }}
            </span>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" id="openAddWidgetModal" class="btn btn-outline btn-sm !bg-white dark:!bg-slate-900 shadow-xs">
                <i class="ph-bold ph-plus"></i> {{ __('Add widget') }}
            </button>
            <button type="button" id="resetDashboardBtn" class="btn btn-outline btn-sm !bg-white dark:!bg-slate-900 text-slate-600 dark:text-slate-300">
                <i class="ph ph-arrow-counter-clockwise"></i> {{ __('Reset') }}
            </button>
            <button type="button" id="cancelDashboardBtn" class="btn btn-ghost btn-sm text-slate-500">
                <i class="ph ph-x"></i> {{ __('Cancel') }}
            </button>
            <button type="button" id="saveDashboardBtn" class="btn btn-sm !bg-slate-900 dark:!bg-white !text-white dark:!text-slate-900 !rounded-xl !px-4 shadow-sm hover:!bg-slate-800 font-bold">
                <i class="ph-bold ph-check"></i> {{ __('Save') }}
            </button>
        </div>
    </div>

    {{-- ============================== CUSTOMIZABLE WIDGET GRID ============================== --}}
    <div id="dashboardWidgetGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 items-start">

        {{-- Widget 1: ENROLLMENT / TOTAL USERS (Size S) --}}
        <div class="dash-widget card p-5 relative group" data-widget-id="w_enrollment" draggable="true">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <div class="flex items-center gap-2 cursor-grab active:cursor-grabbing text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <i class="ph-bold ph-dots-six-vertical text-lg"></i>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ __('USERS OVERVIEW') }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-5 h-5 rounded border border-slate-200 dark:border-slate-700 text-[10px] font-bold text-slate-400 flex items-center justify-center">S</span>
                    <button type="button" class="w-6 h-6 rounded text-slate-400 hover:text-slate-700 dark:hover:text-white flex items-center justify-center" title="Settings"><i class="ph ph-sliders-horizontal text-xs"></i></button>
                    <button type="button" class="w-6 h-6 rounded text-slate-400 hover:text-rose-500 flex items-center justify-center remove-widget-btn" title="Remove widget"><i class="ph ph-trash text-xs"></i></button>
                </div>
            </div>
            <div>
                <h3 class="text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">{{ $totalUsers }}</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ __('registered accounts') }}</p>
                <p class="text-xs font-bold text-emerald-600 dark:text-emerald-400 mt-2 flex items-center gap-1">
                    <i class="ph-bold ph-arrow-up"></i> {{ $activeUsers }} {{ __('active in system') }}
                </p>
            </div>
        </div>

        {{-- Widget 2: ATTENDANCE TODAY / SESSIONS (Size S) --}}
        <div class="dash-widget card p-5 relative group" data-widget-id="w_attendance" draggable="true">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <div class="flex items-center gap-2 cursor-grab active:cursor-grabbing text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <i class="ph-bold ph-dots-six-vertical text-lg"></i>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ __('STAFF SESSIONS') }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-5 h-5 rounded border border-slate-200 dark:border-slate-700 text-[10px] font-bold text-slate-400 flex items-center justify-center">S</span>
                    <button type="button" class="w-6 h-6 rounded text-slate-400 hover:text-slate-700 dark:hover:text-white flex items-center justify-center" title="Settings"><i class="ph ph-sliders-horizontal text-xs"></i></button>
                    <button type="button" class="w-6 h-6 rounded text-slate-400 hover:text-rose-500 flex items-center justify-center remove-widget-btn" title="Remove widget"><i class="ph ph-trash text-xs"></i></button>
                </div>
            </div>
            <div>
                <h3 class="text-4xl font-extrabold text-slate-400 dark:text-slate-600">—</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $adminsCount }} {{ __('administrators logged in today') }}</p>
            </div>
        </div>

        {{-- Widget 3: IMMUNIZATION / SYSTEM COMPLIANCE (Size M) --}}
        <div class="dash-widget card p-5 relative group" data-widget-id="w_compliance" draggable="true">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <div class="flex items-center gap-2 cursor-grab active:cursor-grabbing text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <i class="ph-bold ph-dots-six-vertical text-lg"></i>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ __('SECURITY COMPLIANCE') }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-5 h-5 rounded border border-slate-200 dark:border-slate-700 text-[10px] font-bold text-slate-400 flex items-center justify-center">M</span>
                    <button type="button" class="w-6 h-6 rounded text-slate-400 hover:text-slate-700 dark:hover:text-white flex items-center justify-center" title="Settings"><i class="ph ph-sliders-horizontal text-xs"></i></button>
                    <button type="button" class="w-6 h-6 rounded text-slate-400 hover:text-rose-500 flex items-center justify-center remove-widget-btn" title="Remove widget"><i class="ph ph-trash text-xs"></i></button>
                </div>
            </div>
            <div>
                <h3 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">95% compliant</h3>
                
                {{-- Horizontal Bar (Green & Red) --}}
                <div class="h-2.5 w-full rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden flex my-3">
                    <div class="h-full bg-emerald-500 rounded-l-full" style="width: 95%"></div>
                    <div class="h-full bg-rose-500 rounded-r-full" style="width: 5%"></div>
                </div>

                <div class="grid grid-cols-2 gap-y-1.5 text-xs font-semibold pt-1">
                    <div class="flex items-center justify-between pr-3">
                        <span class="flex items-center gap-1.5 text-slate-600 dark:text-slate-300"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Compliant</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ $activeUsers }}</span>
                    </div>
                    <div class="flex items-center justify-between pl-3">
                        <span class="flex items-center gap-1.5 text-slate-600 dark:text-slate-300"><span class="w-2 h-2 rounded-full bg-amber-500"></span> Partial</span>
                        <span class="font-bold text-slate-900 dark:text-white">0</span>
                    </div>
                    <div class="flex items-center justify-between pr-3">
                        <span class="flex items-center gap-1.5 text-slate-600 dark:text-slate-300"><span class="w-2 h-2 rounded-full bg-rose-500"></span> Missing</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ $inactiveUsers }}</span>
                    </div>
                    <div class="flex items-center justify-between pl-3">
                        <span class="flex items-center gap-1.5 text-slate-600 dark:text-slate-300"><span class="w-2 h-2 rounded-full bg-rose-700"></span> Expired / invalid</span>
                        <span class="font-bold text-slate-900 dark:text-white">0</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Widget 4: REGISTRATION PIPELINE (Size M) --}}
        <div class="dash-widget card p-5 relative group" data-widget-id="w_pipeline" draggable="true">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <div class="flex items-center gap-2 cursor-grab active:cursor-grabbing text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <i class="ph-bold ph-dots-six-vertical text-lg"></i>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ __('REGISTRATION PIPELINE') }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-5 h-5 rounded border border-slate-200 dark:border-slate-700 text-[10px] font-bold text-slate-400 flex items-center justify-center">M</span>
                    <button type="button" class="w-6 h-6 rounded text-slate-400 hover:text-slate-700 dark:hover:text-white flex items-center justify-center" title="Settings"><i class="ph ph-sliders-horizontal text-xs"></i></button>
                    <button type="button" class="w-6 h-6 rounded text-slate-400 hover:text-rose-500 flex items-center justify-center remove-widget-btn" title="Remove widget"><i class="ph ph-trash text-xs"></i></button>
                </div>
            </div>
            <div>
                <p class="text-xs text-slate-400 mb-3">{{ __('Pipeline status overview') }}</p>
                <div class="grid grid-cols-2 gap-y-2 text-xs font-semibold">
                    <div class="flex items-center justify-between pr-3">
                        <span class="flex items-center gap-1.5 text-slate-600 dark:text-slate-300"><span class="w-2 h-2 rounded-full bg-blue-500"></span> Sent</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ $superAdminsCount }}</span>
                    </div>
                    <div class="flex items-center justify-between pl-3">
                        <span class="flex items-center gap-1.5 text-slate-600 dark:text-slate-300"><span class="w-2 h-2 rounded-full bg-amber-500"></span> In progress</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ $adminsCount }}</span>
                    </div>
                    <div class="flex items-center justify-between pr-3">
                        <span class="flex items-center gap-1.5 text-slate-600 dark:text-slate-300"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Completed</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ $totalUsers }}</span>
                    </div>
                    <div class="flex items-center justify-between pl-3">
                        <span class="flex items-center gap-1.5 text-slate-600 dark:text-slate-300"><span class="w-2 h-2 rounded-full bg-purple-500"></span> On hold</span>
                        <span class="font-bold text-slate-900 dark:text-white">0</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Widget 5: NEW APPLICATIONS / RECENT USERS (Size M) --}}
        <div class="dash-widget card p-5 relative group" data-widget-id="w_applications" draggable="true">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <div class="flex items-center gap-2 cursor-grab active:cursor-grabbing text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <i class="ph-bold ph-dots-six-vertical text-lg"></i>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ __('NEW APPLICATIONS') }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-5 h-5 rounded border border-slate-200 dark:border-slate-700 text-[10px] font-bold text-slate-400 flex items-center justify-center">M</span>
                    <button type="button" class="w-6 h-6 rounded text-slate-400 hover:text-slate-700 dark:hover:text-white flex items-center justify-center" title="Settings"><i class="ph ph-sliders-horizontal text-xs"></i></button>
                    <button type="button" class="w-6 h-6 rounded text-slate-400 hover:text-rose-500 flex items-center justify-center remove-widget-btn" title="Remove widget"><i class="ph ph-trash text-xs"></i></button>
                </div>
            </div>
            <div>
                <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">{{ $recentUsers->count() }}</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 mb-3">{{ __('waiting for review') }}</p>

                <div class="space-y-1.5">
                    @foreach($recentUsers->take(3) as $u)
                        <div class="text-xs text-slate-600 dark:text-slate-300 flex items-center justify-between py-1 border-b border-slate-50 dark:border-slate-800/60">
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $u->name }}</span>
                            <span class="text-slate-400 text-[11px]">{{ $u->created_at ? $u->created_at->diffForHumans() : 'recently' }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Widget 6: COLLECTION RATE (Size S) --}}
        <div class="dash-widget card p-5 relative group" data-widget-id="w_collection" draggable="true">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <div class="flex items-center gap-2 cursor-grab active:cursor-grabbing text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <i class="ph-bold ph-dots-six-vertical text-lg"></i>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ __('COLLECTION RATE') }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-5 h-5 rounded border border-slate-200 dark:border-slate-700 text-[10px] font-bold text-slate-400 flex items-center justify-center">S</span>
                    <button type="button" class="w-6 h-6 rounded text-slate-400 hover:text-slate-700 dark:hover:text-white flex items-center justify-center" title="Settings"><i class="ph ph-sliders-horizontal text-xs"></i></button>
                    <button type="button" class="w-6 h-6 rounded text-slate-400 hover:text-rose-500 flex items-center justify-center remove-widget-btn" title="Remove widget"><i class="ph ph-trash text-xs"></i></button>
                </div>
            </div>
            <div>
                <h3 class="text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">100%</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ __('system health & integrity') }}</p>
                <p class="text-xs font-bold text-emerald-600 dark:text-emerald-400 mt-2">PHP {{ $systemInfo['php_version'] }} · v{{ $systemInfo['laravel_version'] }}</p>
            </div>
        </div>

        {{-- Widget 7: MY TASKS (Size M) --}}
        <div class="dash-widget card p-5 relative group" data-widget-id="w_tasks" draggable="true">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <div class="flex items-center gap-2 cursor-grab active:cursor-grabbing text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <i class="ph-bold ph-dots-six-vertical text-lg"></i>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ __('MY TASKS') }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-5 h-5 rounded border border-slate-200 dark:border-slate-700 text-[10px] font-bold text-slate-400 flex items-center justify-center">M</span>
                    <button type="button" class="w-6 h-6 rounded text-slate-400 hover:text-slate-700 dark:hover:text-white flex items-center justify-center" title="Settings"><i class="ph ph-sliders-horizontal text-xs"></i></button>
                    <button type="button" class="w-6 h-6 rounded text-slate-400 hover:text-rose-500 flex items-center justify-center remove-widget-btn" title="Remove widget"><i class="ph ph-trash text-xs"></i></button>
                </div>
            </div>
            <div class="space-y-2">
                <a href="{{ route('admin.users.create') }}" class="flex items-center justify-between p-2.5 rounded-xl border border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 transition text-xs font-semibold">
                    <span class="flex items-center gap-2 text-slate-800 dark:text-slate-200"><i class="ph-bold ph-user-plus text-emerald-500"></i> Add new user account</span>
                    <i class="ph ph-caret-right text-slate-400"></i>
                </a>
                <a href="{{ route('admin.roles.create') }}" class="flex items-center justify-between p-2.5 rounded-xl border border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 transition text-xs font-semibold">
                    <span class="flex items-center gap-2 text-slate-800 dark:text-slate-200"><i class="ph-bold ph-shield-plus text-blue-500"></i> Configure role permissions</span>
                    <i class="ph ph-caret-right text-slate-400"></i>
                </a>
                <a href="{{ route('admin.settings.index') }}" class="flex items-center justify-between p-2.5 rounded-xl border border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 transition text-xs font-semibold">
                    <span class="flex items-center gap-2 text-slate-800 dark:text-slate-200"><i class="ph-bold ph-palette text-purple-500"></i> Customize theme & brand colors</span>
                    <i class="ph ph-caret-right text-slate-400"></i>
                </a>
            </div>
        </div>

        {{-- Widget 8: ROLES MATRIX (Size S) --}}
        <div class="dash-widget card p-5 relative group" data-widget-id="w_roles" draggable="true">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <div class="flex items-center gap-2 cursor-grab active:cursor-grabbing text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <i class="ph-bold ph-dots-six-vertical text-lg"></i>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ __('ROLES MATRIX') }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-5 h-5 rounded border border-slate-200 dark:border-slate-700 text-[10px] font-bold text-slate-400 flex items-center justify-center">S</span>
                    <button type="button" class="w-6 h-6 rounded text-slate-400 hover:text-slate-700 dark:hover:text-white flex items-center justify-center" title="Settings"><i class="ph ph-sliders-horizontal text-xs"></i></button>
                    <button type="button" class="w-6 h-6 rounded text-slate-400 hover:text-rose-500 flex items-center justify-center remove-widget-btn" title="Remove widget"><i class="ph ph-trash text-xs"></i></button>
                </div>
            </div>
            <div>
                <h3 class="text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">{{ $totalRoles }}</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $totalPermissions }} {{ __('granular permissions defined') }}</p>
                <div class="mt-2.5 flex items-center gap-2">
                    <span class="badge badge-neutral text-[10px]">{{ $superAdminsCount }} Super</span>
                    <span class="badge badge-neutral text-[10px]">{{ $adminsCount }} Admin</span>
                </div>
            </div>
        </div>

    </div>

</div>

{{-- ============================== ADD WIDGET MODAL ============================== --}}
<div id="addWidgetModal" class="palette-modal hidden" role="dialog" aria-modal="true" aria-label="Add widget">
    <div class="palette-dialog !max-w-md">
        <div class="flex items-center justify-between p-4 border-b border-slate-200 dark:border-slate-800">
            <h3 class="font-extrabold text-slate-900 dark:text-white text-base flex items-center gap-2">
                <i class="ph-duotone ph-plus-circle text-emerald-500 text-xl"></i> {{ __('Add Dashboard Widget') }}
            </h3>
            <button type="button" id="closeAddWidgetModal" class="text-slate-400 hover:text-slate-700 dark:hover:text-white text-lg"><i class="ph ph-x"></i></button>
        </div>
        <div class="p-4 space-y-2.5 max-h-96 overflow-y-auto" id="availableWidgetsList">
            <!-- Dynamically populated if widgets are hidden -->
        </div>
    </div>
</div>

@push('styles')
<style>
    .dash-widget { transition: transform 0.15s ease, box-shadow 0.15s ease; }
    .dash-widget.is-dragging { opacity: 0.4; transform: scale(0.98); border-style: dashed; }
    .dash-widget.drag-over { border-color: #10b981; box-shadow: 0 0 0 2px rgba(16,185,129,0.3); }
</style>
@endpush

@push('scripts')
<script>
(function() {
    // 1. Live Digital Clock
    function updateClock() {
        const now = new Date();
        const clockEl = document.getElementById('liveTimeClock');
        if (clockEl) {
            let hours = now.getHours();
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12;
            clockEl.textContent = `${hours}:${minutes}:${seconds} ${ampm}`;
        }
    }
    setInterval(updateClock, 1000);
    updateClock();

    // 2. Drag & Drop Widget Customization
    const grid = document.getElementById('dashboardWidgetGrid');
    let draggedItem = null;

    function initDragAndDrop() {
        const widgets = document.querySelectorAll('.dash-widget');
        widgets.forEach(widget => {
            widget.addEventListener('dragstart', function(e) {
                draggedItem = widget;
                setTimeout(() => widget.classList.add('is-dragging'), 0);
            });

            widget.addEventListener('dragend', function() {
                widget.classList.remove('is-dragging');
                document.querySelectorAll('.dash-widget').forEach(w => w.classList.remove('drag-over'));
                draggedItem = null;
            });

            widget.addEventListener('dragover', function(e) {
                e.preventDefault();
                if (draggedItem && draggedItem !== widget) {
                    widget.classList.add('drag-over');
                }
            });

            widget.addEventListener('dragleave', function() {
                widget.classList.remove('drag-over');
            });

            widget.addEventListener('drop', function(e) {
                e.preventDefault();
                widget.classList.remove('drag-over');
                if (draggedItem && draggedItem !== widget) {
                    const all = Array.from(grid.querySelectorAll('.dash-widget'));
                    const draggedIdx = all.indexOf(draggedItem);
                    const targetIdx = all.indexOf(widget);

                    if (draggedIdx < targetIdx) {
                        grid.insertBefore(draggedItem, widget.nextSibling);
                    } else {
                        grid.insertBefore(draggedItem, widget);
                    }
                }
            });
        });
    }
    initDragAndDrop();

    // 3. Remove Widget Handler
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.remove-widget-btn');
        if (!btn) return;
        const widget = btn.closest('.dash-widget');
        if (widget) {
            widget.style.display = 'none';
            updateWidgetCount();
            if (window.AdminToast) {
                window.AdminToast.show('info', 'Widget removed from view. Click Save to persist.', 'Dashboard');
            }
        }
    });

    function updateWidgetCount() {
        const visible = document.querySelectorAll('.dash-widget:not([style*="display: none"])').length;
        const countEl = document.getElementById('activeWidgetCount');
        if (countEl) countEl.textContent = visible;
    }

    // 4. Save Layout to localStorage
    const saveBtn = document.getElementById('saveDashboardBtn');
    if (saveBtn) {
        saveBtn.addEventListener('click', function() {
            const order = [];
            const hidden = [];
            document.querySelectorAll('.dash-widget').forEach(w => {
                const id = w.getAttribute('data-widget-id');
                order.push(id);
                if (w.style.display === 'none') hidden.push(id);
            });

            localStorage.setItem('admin_dash_widget_order', JSON.stringify(order));
            localStorage.setItem('admin_dash_widget_hidden', JSON.stringify(hidden));

            if (window.AdminToast) {
                window.AdminToast.show('success', 'Custom dashboard layout saved!', 'Saved');
            }
        });
    }

    // 5. Restore saved layout
    function restoreSavedLayout() {
        try {
            const order = JSON.parse(localStorage.getItem('admin_dash_widget_order') || '[]');
            const hidden = JSON.parse(localStorage.getItem('admin_dash_widget_hidden') || '[]');

            if (order.length > 0) {
                order.forEach(id => {
                    const el = document.querySelector(`[data-widget-id="${id}"]`);
                    if (el) grid.appendChild(el);
                });
            }

            hidden.forEach(id => {
                const el = document.querySelector(`[data-widget-id="${id}"]`);
                if (el) el.style.display = 'none';
            });

            updateWidgetCount();
        } catch(e) {}
    }
    restoreSavedLayout();

    // 6. Reset & Cancel Buttons
    const resetBtn = document.getElementById('resetDashboardBtn');
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            localStorage.removeItem('admin_dash_widget_order');
            localStorage.removeItem('admin_dash_widget_hidden');
            document.querySelectorAll('.dash-widget').forEach(w => w.style.display = 'block');
            updateWidgetCount();
            if (window.AdminToast) {
                window.AdminToast.show('info', 'Default layout restored.', 'Reset');
            }
        });
    }

    const cancelBtn = document.getElementById('cancelDashboardBtn');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            restoreSavedLayout();
            if (window.AdminToast) {
                window.AdminToast.show('info', 'Changes cancelled.', 'Cancelled');
            }
        });
    }

    // 7. Add Widget Modal
    const addModal = document.getElementById('addWidgetModal');
    const openAddBtn = document.getElementById('openAddWidgetModal');
    const closeAddBtn = document.getElementById('closeAddWidgetModal');
    const availableList = document.getElementById('availableWidgetsList');

    if (openAddBtn && addModal) {
        openAddBtn.addEventListener('click', function() {
            availableList.innerHTML = '';
            const allWidgets = document.querySelectorAll('.dash-widget');
            let hasHidden = false;

            allWidgets.forEach(w => {
                const isHidden = w.style.display === 'none';
                const id = w.getAttribute('data-widget-id');
                const title = w.querySelector('.uppercase')?.textContent || id;

                const item = document.createElement('div');
                item.className = 'flex items-center justify-between p-3 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50';
                item.innerHTML = `
                    <span class="font-bold text-xs text-slate-800 dark:text-slate-200">${title}</span>
                    <button type="button" class="btn btn-sm ${isHidden ? 'btn-primary' : 'btn-outline'}" data-toggle-widget="${id}">
                        ${isHidden ? '<i class="ph-bold ph-plus"></i> Add' : '<i class="ph-bold ph-check"></i> Added'}
                    </button>
                `;
                availableList.appendChild(item);
                if (isHidden) hasHidden = true;
            });

            addModal.classList.remove('hidden');
        });
    }

    if (closeAddBtn && addModal) {
        closeAddBtn.addEventListener('click', () => addModal.classList.add('hidden'));
    }

    document.addEventListener('click', function(e) {
        const toggleBtn = e.target.closest('[data-toggle-widget]');
        if (!toggleBtn) return;
        const id = toggleBtn.getAttribute('data-toggle-widget');
        const widget = document.querySelector(`[data-widget-id="${id}"]`);
        if (widget) {
            widget.style.display = widget.style.display === 'none' ? 'block' : 'none';
            toggleBtn.className = widget.style.display === 'none' ? 'btn btn-sm btn-primary' : 'btn btn-sm btn-outline';
            toggleBtn.innerHTML = widget.style.display === 'none' ? '<i class="ph-bold ph-plus"></i> Add' : '<i class="ph-bold ph-check"></i> Added';
            updateWidgetCount();
        }
    });

    if (addModal) {
        addModal.addEventListener('click', function(e) {
            if (e.target === addModal) addModal.classList.add('hidden');
        });
    }
})();
</script>
@endpush
@endsection
