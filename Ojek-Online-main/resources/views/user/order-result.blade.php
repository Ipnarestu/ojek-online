@extends('layouts.main')

@section('content')
<div class="max-w-lg mx-auto mt-14 px-4">
    <div class="rounded-2xl bg-gray-900/80 backdrop-blur border border-gray-800 shadow-xl p-8 text-center">

        @if(session('success'))
            <div class="text-green-400 text-5xl mb-4">✅</div>
            <h2 class="text-2xl font-bold text-white mb-2">Driver Ditemukan!</h2>
            <p class="text-gray-400 mb-4">{{ session('success') }}</p>
            <div class="bg-gray-800 rounded-xl p-4 mb-6">
                <p class="text-white">🚗 Driver: {{ session('driver_name') ?? 'Driver' }}</p>
                <p class="text-gray-400 text-sm">Menuju lokasi Anda...</p>
            </div>
            <a href="{{ route('user.order.track', session('order_id')) }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl transition">
                Lacak Pesanan
            </a>
        @elseif(session('warning'))
            <div class="text-yellow-400 text-5xl mb-4">⏳</div>
            <h2 class="text-2xl font-bold text-white mb-2">Menunggu Driver</h2>
            <p class="text-gray-400 mb-4">{{ session('warning') }}</p>
            <a href="{{ route('user.home') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-xl transition">
                Kembali ke Beranda
            </a>
        @endif

    </div>
</div>
@endsection