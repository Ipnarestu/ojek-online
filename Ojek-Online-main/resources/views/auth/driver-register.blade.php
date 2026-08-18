@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-8
            bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">

    <div class="w-full max-w-md
                bg-white/5 backdrop-blur-xl
                border border-white/10
                rounded-2xl shadow-2xl
                p-8">

        <div class="text-center mb-8">
            <div class="mx-auto mb-4 w-16 h-16 rounded-full
                        bg-green-600/20 flex items-center justify-center
                        text-3xl text-green-400">
                🛵
            </div>
            <h2 class="text-2xl font-bold text-white">
                Daftar sebagai Driver
            </h2>
            <p class="text-sm text-gray-400 mt-1">
                Bergabung dan mulai menerima pesanan
            </p>
            <p class="text-xs text-yellow-400 mt-2">
                ⚠️ Akun driver akan diverifikasi oleh admin terlebih dahulu
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded-xl bg-red-600/20 border border-red-500/30 text-red-300 px-4 py-3 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('driver.register.submit') }}" 
              enctype="multipart/form-data" class="space-y-4">
            @csrf

            {{-- NAMA --}}
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">
                    Nama Lengkap <span class="text-red-400">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       placeholder="Nama sesuai KTP"
                       class="w-full px-4 py-3 rounded-xl bg-gray-900/60 border border-white/10
                              text-white placeholder-gray-500 focus:ring-2 focus:ring-green-600">
            </div>

            {{-- EMAIL --}}
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">
                    Email <span class="text-red-400">*</span>
                </label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       placeholder="email@contoh.com"
                       class="w-full px-4 py-3 rounded-xl bg-gray-900/60 border border-white/10
                              text-white placeholder-gray-500 focus:ring-2 focus:ring-green-600">
            </div>

            {{-- PHONE --}}
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">
                    Nomor HP <span class="text-red-400">*</span>
                </label>
                <input type="text" name="phone" value="{{ old('phone') }}" required
                       placeholder="08xxxxxxxxxx"
                       class="w-full px-4 py-3 rounded-xl bg-gray-900/60 border border-white/10
                              text-white placeholder-gray-500 focus:ring-2 focus:ring-green-600">
            </div>

            {{-- PASSWORD --}}
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">
                    Password <span class="text-red-400">*</span>
                </label>
                <input type="password" name="password" required
                       placeholder="Minimal 6 karakter"
                       class="w-full px-4 py-3 rounded-xl bg-gray-900/60 border border-white/10
                              text-white placeholder-gray-500 focus:ring-2 focus:ring-green-600">
            </div>

            {{-- UPLOAD DOKUMEN --}}
            <div class="border-t border-gray-700 pt-4 mt-2">
                <p class="text-sm text-gray-300 mb-3 font-semibold">📄 Dokumen Verifikasi</p>
                
                {{-- KTP --}}
                <div class="mb-3">
                    <label class="block text-sm text-gray-400 mb-1">
                        Upload KTP <span class="text-red-400">*</span>
                    </label>
                    <input type="file" name="ktp_path" accept="image/*,.pdf" required
                           class="w-full text-gray-400 file:mr-2 file:py-2 file:px-4
                                  file:rounded-lg file:border-0 file:text-sm
                                  file:bg-green-600 file:text-white
                                  hover:file:bg-green-700">
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, PDF (max 2MB)</p>
                </div>

                {{-- SIM --}}
                <div class="mb-3">
                    <label class="block text-sm text-gray-400 mb-1">
                        Upload SIM <span class="text-red-400">*</span>
                    </label>
                    <input type="file" name="sim_path" accept="image/*,.pdf" required
                           class="w-full text-gray-400 file:mr-2 file:py-2 file:px-4
                                  file:rounded-lg file:border-0 file:text-sm
                                  file:bg-green-600 file:text-white
                                  hover:file:bg-green-700">
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, PDF (max 2MB)</p>
                </div>

                {{-- STNK --}}
                <div class="mb-3">
                    <label class="block text-sm text-gray-400 mb-1">
                        Upload STNK <span class="text-red-400">*</span>
                    </label>
                    <input type="file" name="stnk_path" accept="image/*,.pdf" required
                           class="w-full text-gray-400 file:mr-2 file:py-2 file:px-4
                                  file:rounded-lg file:border-0 file:text-sm
                                  file:bg-green-600 file:text-white
                                  hover:file:bg-green-700">
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, PDF (max 2MB)</p>
                </div>

                {{-- FOTO DIRI --}}
                <div class="mb-3">
                    <label class="block text-sm text-gray-400 mb-1">
                        Upload Foto Diri <span class="text-red-400">*</span>
                    </label>
                    <input type="file" name="photo_path" accept="image/*" required
                           class="w-full text-gray-400 file:mr-2 file:py-2 file:px-4
                                  file:rounded-lg file:border-0 file:text-sm
                                  file:bg-green-600 file:text-white
                                  hover:file:bg-green-700">
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG (max 2MB)</p>
                </div>
            </div>

            <button type="submit"
                    class="w-full py-3 rounded-xl bg-green-600 hover:bg-green-700
                           text-white font-semibold transition shadow-lg">
                Daftar Driver
            </button>
        </form>

        <p class="text-xs text-gray-400 text-center mt-6">
            Dengan mendaftar, Anda menyetujui syarat & ketentuan.
        </p>

        <div class="text-center text-sm text-gray-400 mt-2">
            Sudah punya akun?
            <a href="{{ route('driver.login') }}"
               class="text-green-400 font-semibold hover:underline">
                Login Driver
            </a>
        </div>

    </div>
</div>
@endsection