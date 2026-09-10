<header class="flex h-16 shrink-0 items-center justify-between gap-3 border-b border-slate-200 bg-white px-4 sm:px-6 lg:px-8">
    <button
        x-ref="sidebarTrigger"
        type="button"
        @click="sidebarOpen = true; $nextTick(() => $refs.sidebarClose.focus())"
        class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg text-slate-600 transition-colors hover:bg-slate-100 hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 lg:hidden"
        aria-label="Open navigation"
        aria-controls="app-sidebar"
        :aria-expanded="sidebarOpen"
    >
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>

    <!-- Search Bar -->
    <div class="hidden min-w-0 max-w-lg flex-1 sm:block">
        <form action="{{ route('drive.index') }}" method="GET" class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </span>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search documents..." class="w-full pl-10 pr-4 py-2 bg-slate-100 border-none rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
        </form>
    </div>

    <!-- Right Controls -->
    <div class="flex shrink-0 items-center space-x-3 sm:space-x-6">
        <!-- User Unit badge -->
        @if(auth()->user()->unitKerja)
            <span class="hidden max-w-32 truncate rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-slate-600 sm:inline-flex">
                {{ auth()->user()->unitKerja->kode }}
            </span>
        @endif

        <!-- Profile Dropdown -->
        <div class="relative" x-data="{ open: false }" @keydown.escape.window="if (open) { open = false; $nextTick(() => $refs.profileTrigger.focus()) }">
            <button
                x-ref="profileTrigger"
                @click="open = !open"
                class="flex items-center space-x-2 rounded-lg text-slate-700 transition-colors hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 sm:space-x-3"
                aria-label="Open user menu"
                aria-controls="profile-menu"
                :aria-expanded="open"
            >
                <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center font-bold text-sm text-slate-700 overflow-hidden border border-slate-300">
                    @if(auth()->user()->avatar_path)
                        <img src="{{ Storage::url(auth()->user()->avatar_path) }}" alt="Avatar" class="w-full h-full object-cover">
                    @else
                        {{ substr(auth()->user()->nama, 0, 2) }}
                    @endif
                </div>
                <div class="hidden md:flex flex-col items-start leading-tight">
                    <span class="text-sm font-semibold text-slate-800">{{ auth()->user()->nama }}</span>
                    <span class="text-[11px] text-slate-500 capitalize">{{ auth()->user()->role?->nama }}</span>
                </div>
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <!-- Dropdown menu -->
            <div id="profile-menu" x-cloak x-show="open" @click.away="open = false" x-transition class="absolute right-0 z-50 mt-2 w-48 rounded-xl border border-slate-200 bg-white py-2 shadow-xl">
                <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    My Profile
                </a>
                <hr class="border-slate-100 my-1">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-4 py-2 text-sm text-rose-600 hover:bg-rose-50 text-left">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Sign Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
