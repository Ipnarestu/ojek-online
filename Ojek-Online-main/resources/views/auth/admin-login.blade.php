@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4
            bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">

    {{-- DECORATIVE BACKGROUND (subtle) --}}
    <div class="fixed inset-0 pointer-events-none overflow-hidden">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-purple-600/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-blue-600/10 rounded-full blur-3xl"></div>
    </div>

    {{-- LOGIN CARD --}}
    <div class="relative w-full max-w-md
                bg-white/5 backdrop-blur-xl
                border border-white/10
                rounded-2xl shadow-2xl
                overflow-hidden">

        {{-- TOP ACCENT BAR --}}
        <div class="h-1 bg-gradient-to-r from-purple-500 via-purple-400 to-transparent"></div>

        <div class="p-8">

            {{-- LOGO / ICON --}}
            <div class="flex justify-center mb-6">
                <div class="w-16 h-16 rounded-2xl
                            bg-gradient-to-br from-purple-500/20 to-purple-600/10
                            flex items-center justify-center
                            border border-purple-500/30">
                    <span class="text-3xl">🔐</span>
                </div>
            </div>

            {{-- TITLE --}}
            <div class="text-center mb-8">
                <h2 class="text-2xl font-semibold text-white tracking-tight">
                    OMK ADMIN
                </h2>
                <p class="text-sm text-gray-400 mt-2">
                    Masuk ke panel administrasi
                </p>
            </div>

            {{-- ERROR MESSAGE --}}
            @if ($errors->any())
                <div class="mb-6 rounded-xl
                            bg-red-500/10 border border-red-500/20
                            text-red-400 px-4 py-3 text-sm
                            backdrop-blur">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ $errors->first() }}
                    </div>
                </div>
            @endif

            {{-- LOGIN FORM --}}
            <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-5">
                @csrf

                {{-- EMAIL FIELD --}}
                <div>
                    <label class="block text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">
                        Email Address
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                            </svg>
                        </div>
                        <input type="email"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               placeholder="admin@omk.com"
                               class="w-full pl-10 pr-4 py-3 rounded-xl
                                      bg-gray-900/50 border border-gray-700
                                      text-white placeholder-gray-500
                                      focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500
                                      transition">
                    </div>
                </div>

                {{-- PASSWORD FIELD --}}
                <div>
                    <label class="block text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <input type="password"
                               name="password"
                               required
                               placeholder="••••••••"
                               class="w-full pl-10 pr-4 py-3 rounded-xl
                                      bg-gray-900/50 border border-gray-700
                                      text-white placeholder-gray-500
                                      focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500
                                      transition">
                    </div>
                </div>

                {{-- SUBMIT BUTTON --}}
                <button type="submit"
                        class="w-full py-3 rounded-xl
                               bg-gradient-to-r from-purple-600 to-purple-700
                               hover:from-purple-700 hover:to-purple-800
                               text-white font-semibold
                               transition-all duration-200
                               shadow-lg shadow-purple-600/20">
                    Masuk ke Dashboard
                </button>
            </form>

            {{-- FOOTER NOTE --}}
            <div class="mt-6 text-center">
                <p class="text-xs text-gray-500">
                    © {{ date('Y') }} OMK OJOL — Ojek Mitra Mahasiswa
                </p>
                <p class="text-xs text-gray-600 mt-1">
                    Akses terbatas administrator
                </p>
            </div>

        </div>
    </div>
</div>
@endsection