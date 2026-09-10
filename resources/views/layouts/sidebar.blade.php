<aside
    id="app-sidebar"
    :class="sidebarOpen ? 'translate-x-0 shadow-2xl' : '-translate-x-full shadow-none'"
    :inert="!isDesktop && !sidebarOpen"
    :aria-hidden="(!isDesktop && !sidebarOpen).toString()"
    class="fixed inset-y-0 left-0 z-40 flex w-64 shrink-0 -translate-x-full flex-col bg-slate-900 text-slate-300 transition-transform duration-200 ease-out motion-reduce:transition-none lg:static lg:translate-x-0 lg:shadow-none"
>
    <!-- Logo -->
    <div class="flex h-16 items-center justify-between border-b border-slate-800 px-6">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 rounded-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-900">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-white/20 bg-white p-1 shadow-md shadow-slate-950/30">
                <x-application-logo class="h-full w-full object-contain" />
            </span>
            <span class="font-bold text-lg text-white tracking-wider">SIMADU</span>
        </a>
        <button
            x-ref="sidebarClose"
            type="button"
            @click="closeSidebar()"
            class="-mr-2 inline-flex h-10 w-10 items-center justify-center rounded-lg text-slate-400 transition-colors hover:bg-slate-800 hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-400 lg:hidden"
            aria-label="Close navigation"
        >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <!-- Navigation links -->
    <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-7">
        <!-- Main Section -->
        <div>
            <span class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">My Storage</span>
            <div class="mt-3 space-y-1">
                <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"/></svg>
                    Overview
                </a>
                <a href="{{ route('drive.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('drive.index') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                    My Drive
                </a>
                <a href="{{ route('shares.shared') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('shares.shared') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Shared with me
                </a>
                <a href="{{ route('trash.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('trash.index') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Trash
                </a>
                <a href="{{ route('activity.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('activity.index') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Activity Log
                </a>
            </div>
        </div>

        <!-- Admin Section -->
        @if(auth()->user()->isSuperAdmin())
        <div>
            <span class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Administration</span>
            <div class="mt-3 space-y-1">
                <a href="{{ route('admin.users.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Users
                </a>
                <a href="{{ route('admin.unit-kerja.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.unit-kerja.*') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Unit Kerja
                </a>
                <a href="{{ route('admin.categories.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.categories.*') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Categories
                </a>
                <a href="{{ route('admin.settings.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.settings.index') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Settings
                </a>
            </div>
        </div>
        @endif
    </nav>

    <!-- Quota Visualizer Section -->
    <div class="p-4 border-t border-slate-800 bg-slate-950">
        <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Storage Quota</div>
        @php
            $used = auth()->user()->storage_used;
            $quota = auth()->user()->storage_quota;
            $percentage = $quota > 0 ? min(100, round(($used / $quota) * 100, 2)) : 0;

            // Formatted Strings
            $formattedUsed = number_format($used / 1048576, 2) . ' MB';
            $formattedQuota = $quota > 0 ? (number_format($quota / 1073741824, 2) . ' GB') : 'Unlimited';
        @endphp

        <div class="flex justify-between items-center text-xs text-slate-400 mb-1">
            <span>{{ $formattedUsed }} used</span>
            <span>{{ $formattedQuota }} limit</span>
        </div>

        @if($quota > 0)
        <div class="w-full bg-slate-800 rounded-full h-2 overflow-hidden">
            <div class="bg-indigo-500 h-full rounded-full transition-all duration-300" style="width: {{ $percentage }}%"></div>
        </div>
        <div class="text-right text-[10px] text-slate-500 mt-1">{{ $percentage }}% used</div>
        @else
        <div class="w-full bg-slate-800 rounded-full h-2 overflow-hidden">
            <div class="bg-emerald-500 h-full rounded-full" style="width: 5%"></div>
        </div>
        <div class="text-right text-[10px] text-slate-500 mt-1">Quota: Free tier / Unlimited</div>
        @endif
    </div>
</aside>
