<x-guest-layout>
    <header class="mb-9 text-center sm:mb-10">
        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-indigo-400">SIMADU secure access</p>
        <h2 class="mt-5 text-2xl font-bold tracking-tight text-white sm:text-3xl">Welcome back to SIMADU</h2>
        <p class="mx-auto mt-3 max-w-lg text-base font-semibold text-slate-200 sm:text-lg">Sistem Manajemen Dokumen</p>
        <p class="mx-auto mt-2 max-w-lg text-sm leading-6 text-slate-400 sm:text-base">A clear place for every working document.</p>
    </header>

    <x-auth-session-status class="mb-6 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300" :status="session('status')" />

    @if (session('error'))
        <div class="mb-6 rounded-lg border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm font-medium text-rose-300" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <div>
            <label for="identifier" class="block text-sm font-semibold text-slate-200">{{ __('NIP or E-mail') }}</label>
            <input id="identifier" class="mt-2.5 block w-full rounded-xl border-slate-300 bg-slate-100 px-5 py-4 text-base text-slate-950 shadow-sm transition-colors placeholder:text-slate-400 hover:border-indigo-300 focus:border-indigo-500 focus:bg-white focus:ring-indigo-500" type="text" name="identifier" value="{{ old('identifier') }}" placeholder="NIP or E-mail" required autofocus autocomplete="username">
            <x-input-error :messages="$errors->get('identifier')" class="mt-2 text-rose-300" />
        </div>

        <div>
            <label for="password" class="block text-sm font-semibold text-slate-200">{{ __('Password') }}</label>
            <input id="password" class="mt-2.5 block w-full rounded-xl border-slate-300 bg-slate-100 px-5 py-4 text-base text-slate-950 shadow-sm transition-colors placeholder:text-slate-400 hover:border-indigo-300 focus:border-indigo-500 focus:bg-white focus:ring-indigo-500" type="password" name="password" placeholder="Enter your password" required autocomplete="current-password">
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-rose-300" />
        </div>

        <div class="flex flex-wrap items-center justify-between gap-x-4 gap-y-3 pt-1">
            <label for="remember_me" class="inline-flex cursor-pointer items-center gap-2.5 text-sm text-slate-300">
                <input id="remember_me" type="checkbox" class="h-5 w-5 rounded border-slate-500 bg-slate-100 text-indigo-600 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-slate-900" name="remember">
                <span>{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="rounded-md text-sm font-semibold text-indigo-400 transition-colors hover:text-indigo-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-900" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <button type="submit" class="inline-flex h-14 w-full items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-base font-semibold text-white shadow-sm shadow-indigo-950/30 transition-colors hover:bg-indigo-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-900 active:bg-indigo-800">
            {{ __('Log in') }}
        </button>
    </form>
</x-guest-layout>
