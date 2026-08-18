@extends('layouts.driver')

@section('content')
<div class="max-w-6xl mx-auto mt-10 px-4">
    <div class="rounded-2xl bg-gray-900/80 backdrop-blur border border-gray-800 shadow-xl p-8">

        {{-- HEADER --}}
        <div class="mb-6 text-center">
            <h2 class="text-2xl font-semibold text-white">Riwayat Pesanan</h2>
            <p class="text-sm text-gray-400 mt-1">
                Semua pesanan yang telah Anda selesaikan
            </p>
        </div>

        {{-- SESSION MESSAGES --}}
        @if(session('success'))
            <div class="bg-green-500/20 border border-green-500 text-green-400 rounded-xl p-4 mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="bg-yellow-500/20 border border-yellow-500 text-yellow-400 rounded-xl p-4 mb-6">
                {{ session('warning') }}
            </div>
        @endif

        {{-- STATISTIK --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="bg-green-900/30 rounded-xl p-4 text-center border border-green-700/30">
                <p class="text-gray-400 text-sm">Total Pendapatan</p>
                <p class="text-2xl font-bold text-green-400">Rp {{ number_format($totalEarnings ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="bg-yellow-900/30 rounded-xl p-4 text-center border border-yellow-700/30">
                <p class="text-gray-400 text-sm">Total Tip</p>
                <p class="text-2xl font-bold text-yellow-400">Rp {{ number_format($totalTips ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>

        {{-- DAFTAR PESANAN --}}
        @if($orders->count() > 0)
            <div class="space-y-4">
                @foreach($orders as $order)
                <div class="bg-gray-800/50 rounded-xl p-5 border border-gray-700">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 flex-wrap">
                                <p class="text-white font-semibold">Order #{{ $order->id }}</p>
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-green-500/20 text-green-400">
                                    SELESAI
                                </span>
                            </div>
                            <p class="text-gray-400 text-sm mt-2">
                                📅 {{ \Carbon\Carbon::parse($order->completed_at ?? $order->created_at)->translatedFormat('d M Y, H:i') }}
                            </p>
                            <p class="text-gray-400 text-sm">
                                📍 {{ Str::limit($order->pickup_location, 40) }} → {{ Str::limit($order->destination, 40) }}
                            </p>
                            <p class="text-green-400 font-semibold mt-1">
                                Rp {{ number_format($order->price, 0, ',', '.') }}
                            </p>
                            @if($order->tip_amount > 0)
                            <p class="text-yellow-400 text-sm">
                                + Tip: Rp {{ number_format($order->tip_amount, 0, ',', '.') }}
                            </p>
                            @endif
                        </div>

                        <div class="flex flex-col gap-2 min-w-[100px]">
                            <a href="{{ route('driver.order.detail', $order->id) }}" 
                               class="bg-gray-700 hover:bg-gray-600 text-white text-center px-4 py-2 rounded-lg text-sm transition">
                                Detail
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- PAGINATION --}}
            @if(method_exists($orders, 'links'))
                <div class="mt-6">
                    {{ $orders->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <div class="text-5xl mb-4">📭</div>
                <p class="text-gray-400">Belum ada pesanan selesai</p>
                <p class="text-gray-500 text-sm mt-2">Pesanan yang sudah Anda selesaikan akan muncul di sini</p>
                <a href="{{ route('driver.dashboard') }}" 
                   class="inline-block mt-4 bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-xl transition">
                    Kembali ke Dashboard
                </a>
            </div>
        @endif

        {{-- BACK BUTTON --}}
        <div class="mt-6 text-center">
            <a href="{{ route('driver.dashboard') }}" class="text-sm text-gray-400 hover:text-white transition">
                ← Kembali ke Dashboard
            </a>
        </div>

    </div>
</div>
@endsection