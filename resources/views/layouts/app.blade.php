<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SIMADU') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-50 text-slate-800">
        <div
            x-data="{
                sidebarOpen: false,
                isDesktop: window.innerWidth >= 1024,
                closeSidebar() {
                    this.sidebarOpen = false;
                    requestAnimationFrame(() => this.$refs.sidebarTrigger.focus());
                },
                syncSidebarViewport() {
                    this.isDesktop = window.innerWidth >= 1024;
                    if (this.isDesktop) {
                        this.sidebarOpen = false;
                    }
                }
            }"
            x-effect="document.body.classList.toggle('overflow-hidden', sidebarOpen)"
            @resize.window="syncSidebarViewport()"
            @keydown.escape.window="if (sidebarOpen) closeSidebar()"
            class="flex min-h-screen overflow-x-hidden"
        >
            <div
                x-cloak
                x-show="sidebarOpen"
                @click="closeSidebar()"
                x-transition:enter="transition-opacity ease-out duration-200 motion-reduce:transition-none"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-in duration-150 motion-reduce:transition-none"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-30 bg-slate-950/60 lg:hidden"
                aria-hidden="true"
            ></div>
            <!-- Sidebar -->
            @include('layouts.sidebar')

            <!-- Main Content Container -->
            <div :inert="sidebarOpen" class="flex min-w-0 flex-1 flex-col">
                <!-- Top Header -->
                @include('layouts.header')

                <!-- Page Content -->
                <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 sm:p-6 md:p-8">
                    <!-- Session Status Alerts -->
                    @if (session('success'))
                        <div class="mb-6 flex items-start rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800 shadow-sm sm:items-center">
                            <svg class="w-5 h-5 mr-3 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-6 flex items-start rounded-xl border border-rose-200 bg-rose-50 p-4 text-rose-800 shadow-sm sm:items-center">
                            <svg class="w-5 h-5 mr-3 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
