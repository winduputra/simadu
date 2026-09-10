<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SIMADU') }}</title>
        <link rel="icon" href="{{ asset('simadu.ico') }}" sizes="any">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            .simadu-auth-canvas {
                box-sizing: border-box;
                min-height: 100vh;
                min-height: 100svh;
                width: 100%;
                overflow-x: hidden;
                overflow-y: auto;
                padding: clamp(6rem, 12vh, 8rem) 1rem clamp(2.5rem, 6vh, 4rem);
                background-color: #0f172a;
                background-image:
                    radial-gradient(circle at 14% 12%, rgb(56 189 248 / 45%), transparent 36rem),
                    radial-gradient(circle at 86% 82%, rgb(99 102 241 / 42%), transparent 34rem),
                    linear-gradient(135deg, #0f172a 0%, #312e81 40%, #2563eb 72%, #38bdf8 100%);
            }

            .simadu-auth-shell {
                box-sizing: border-box;
                width: min(100%, 46rem);
                padding-top: clamp(4.75rem, 9vw, 6rem);
            }

            .simadu-auth-panel {
                box-sizing: border-box;
                position: relative;
                width: 100%;
                padding: clamp(6.75rem, 14vw, 8.25rem) clamp(1.5rem, 5vw, 3.5rem) clamp(2.75rem, 6vw, 3.5rem);
            }

            .simadu-auth-logo {
                position: absolute;
                top: 0;
                left: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                width: clamp(9.5rem, 22vw, 12rem);
                aspect-ratio: 1;
                transform: translate(-50%, -50%);
                background-color: #ffffff;
                box-shadow: 0 24px 60px rgb(15 23 42 / 28%);
            }

            .simadu-auth-logo img {
                width: 100%;
                height: 100%;
                object-fit: contain;
            }
        </style>
    </head>
    <body class="bg-slate-900 font-sans text-white antialiased">
        <main class="simadu-auth-canvas relative flex items-center justify-center">
            <div class="simadu-auth-shell relative">
                <section class="simadu-auth-panel rounded-2xl border border-slate-800 bg-slate-900 shadow-sm">
                    <a href="/" class="simadu-auth-logo overflow-hidden rounded-2xl border border-slate-200 p-4 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400 focus-visible:ring-offset-4 focus-visible:ring-offset-slate-900 sm:p-5">
                        <x-application-logo class="h-full w-full object-contain" />
                    </a>

                    {{ $slot }}
                </section>
            </div>
        </main>
    </body>
</html>
