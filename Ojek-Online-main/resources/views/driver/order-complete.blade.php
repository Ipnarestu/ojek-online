@extends('layouts.driver')

@section('content')
<div class="max-w-2xl mx-auto mt-10 px-4">
    <div class="bg-gray-900/80 backdrop-blur border border-gray-800 rounded-2xl p-8">

        <div class="text-center mb-6">
            <div class="w-16 h-16 mx-auto bg-green-500/20 rounded-full flex items-center justify-center text-3xl mb-4">
                ✅
            </div>
            <h2 class="text-2xl font-bold text-white">Pesanan Selesai!</h2>
            <p class="text-gray-400 mt-1">Anda telah berhasil menyelesaikan pesanan</p>
        </div>

        <div class="bg-green-500/10 border border-green-500/30 rounded-xl p-4 mb-6">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-green-400 font-semibold">Driver telah menyelesaikan pesanan</p>
            </div>
            <p class="text-gray-400 text-sm mt-2 ml-8">
                Pesanan ini telah selesai dan akan masuk ke Riwayat Pesanan.
            </p>
        </div>

        {{-- INFORMASI PENDAPATAN --}}
        <div class="bg-gradient-to-r from-green-900/30 to-green-800/20 rounded-xl p-6 mb-6 text-center border border-green-700/30">
            <p class="text-gray-400 text-sm">Pendapatan dari pesanan ini</p>
            <p class="text-3xl font-bold text-green-400 mt-1">
                Rp {{ number_format($order->driver_earning ?? $order->price * 0.92, 0, ',', '.') }}
            </p>
            <div class="text-xs text-gray-500 mt-2 space-y-1">
                <p> Harga Pesanan: Rp {{ number_format($order->price, 0, ',', '.') }}</p>
                <p class="text-red-400"> Potongan Admin (8%): - Rp {{ number_format($order->admin_fee ?? $order->price * 0.08, 0, ',', '.') }}</p>
                <p class="text-green-400"> Pendapatan Bersih Driver (92%): Rp {{ number_format($order->driver_earning ?? $order->price * 0.92, 0, ',', '.') }}</p>
            </div>
            @if($order->tip_amount > 0)
            <p class="text-sm text-yellow-400 mt-2">
                + Tip: Rp {{ number_format($order->tip_amount, 0, ',', '.') }}
            </p>
            @endif
        </div>

        <div class="bg-gray-800/50 rounded-xl p-5 mb-6">
            <h3 class="text-lg font-semibold text-white mb-4">Detail Pesanan</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-400">ID Pesanan</span>
                    <span class="text-white font-medium">#{{ $order->id }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Titik Jemput</span>
                    <span class="text-white text-right max-w-[200px]">{{ Str::limit($order->pickup_location, 40) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Tujuan</span>
                    <span class="text-white text-right max-w-[200px]">{{ Str::limit($order->destination, 40) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Metode Pembayaran</span>
                    <span class="text-white uppercase">{{ $order->payment_method }}</span>
                </div>
            </div>
        </div>

        <div class="bg-gray-800/50 rounded-xl p-5 mb-6">
            <h3 class="text-lg font-semibold text-white mb-4">Waktu Perjalanan</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-400">Pesanan Diterima</span>
                    <span class="text-white">{{ \Carbon\Carbon::parse($order->created_at)->translatedFormat('d M Y, H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Pesanan Selesai</span>
                    <span class="text-white">{{ \Carbon\Carbon::parse($order->completed_at ?? now())->translatedFormat('d M Y, H:i') }}</span>
                </div>
                <div class="flex justify-between pt-2 border-t border-gray-700">
                    <span class="text-gray-400">Durasi</span>
                    <span class="text-green-400 font-semibold">
                        {{ \Carbon\Carbon::parse($order->created_at)->diffForHumans($order->completed_at ?? now(), ['parts' => 2, 'short' => false]) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="bg-gray-800/50 rounded-xl p-5 mb-6">
            <h3 class="text-lg font-semibold text-white mb-3">Informasi Pemesan</h3>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-400">Nama</span>
                    <span class="text-white">{{ $order->user->name ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Email</span>
                    <span class="text-white">{{ $order->user->email ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Telepon</span>
                    <span class="text-white">{{ $order->user->phone ?? '-' }}</span>
                </div>
            </div>
        </div>

        {{-- TOMBOL --}}
        <div class="flex gap-3">
            <a href="{{ route('driver.orders.history') }}" 
               class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-xl transition text-center">
                Lihat Riwayat Pesanan
            </a>
            <a href="{{ route('driver.dashboard') }}" 
               class="flex-1 bg-gray-700 hover:bg-gray-600 text-white font-semibold py-3 rounded-xl transition text-center">
                Ke Dashboard
            </a>
        </div>

    </div>
</div>
@endsection