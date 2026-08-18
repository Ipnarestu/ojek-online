@extends('layouts.main')

@section('content')
<div class="max-w-5xl mx-auto mt-10 px-4">
    <div class="rounded-2xl bg-gray-900/80 backdrop-blur border border-gray-800 shadow-xl p-8">

        {{-- HEADER --}}
        <div class="mb-6 text-center">
            <h2 class="text-2xl font-semibold text-white">Riwayat Pesanan</h2>
            <p class="text-sm text-gray-400 mt-1">
                Semua pesanan yang pernah Anda buat
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

        @if(session('error'))
            <div class="bg-red-500/20 border border-red-500 text-red-400 rounded-xl p-4 mb-6">
                {{ session('error') }}
            </div>
        @endif

        {{-- DAFTAR PESANAN --}}
        @if($orders->count() > 0)
            <div class="space-y-4">
                @foreach($orders as $order)
                <div class="bg-gray-800/50 rounded-xl p-5 border border-gray-700">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        {{-- INFO PESANAN --}}
                        <div class="flex-1">
                            <div class="flex items-center gap-3 flex-wrap">
                                <p class="text-white font-semibold">Order #{{ $order->id }}</p>
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                    @if($order->status === 'pending') bg-yellow-500/20 text-yellow-400
                                    @elseif($order->status === 'accepted') bg-blue-500/20 text-blue-400
                                    @elseif($order->status === 'on_trip') bg-purple-500/20 text-purple-400
                                    @elseif($order->status === 'completed') bg-green-500/20 text-green-400
                                    @else bg-gray-500/20 text-gray-400
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                </span>
                            </div>
                            <p class="text-gray-400 text-sm mt-2">
                                📅 {{ \Carbon\Carbon::parse($order->created_at)->translatedFormat('d M Y, H:i') }}
                            </p>
                            <p class="text-gray-400 text-sm">
                                📍 {{ Str::limit($order->pickup_location, 40) }} → {{ Str::limit($order->destination, 40) }}
                            </p>
                            <p class="text-green-400 font-semibold mt-1">
                                Rp {{ number_format($order->price, 0, ',', '.') }}
                            </p>
                            <p class="text-gray-500 text-xs">
                                💳 {{ strtoupper($order->payment_method) }}
                            </p>
                        </div>

                        {{-- TOMBOL AKSI --}}
                        <div class="flex flex-col gap-2 min-w-[120px]">
                            <a href="{{ route('user.order.detail', $order->id) }}" 
                               class="bg-gray-700 hover:bg-gray-600 text-white text-center px-4 py-2 rounded-lg text-sm transition">
                                Detail
                            </a>

                            @if($order->status === 'completed')
                                @php
                                    $hasRated = \App\Models\Rating::where('pesanan_id', $order->id)->exists();
                                @endphp
                                
                                @if($hasRated)
                                    <span class="bg-green-600/20 text-green-400 text-center px-4 py-2 rounded-lg text-sm">
                                        ⭐ Sudah dinilai
                                    </span>
                                @else
                                    <a href="{{ route('user.order.rating', $order->id) }}" 
                                       class="bg-yellow-600 hover:bg-yellow-700 text-white text-center px-4 py-2 rounded-lg text-sm transition">
                                        ⭐ Beri Rating
                                    </a>
                                @endif
                            @endif

                            @if($order->status === 'pending' || $order->status === 'accepted' || $order->status === 'on_trip')
                                <a href="{{ route('user.order.track', $order->id) }}" 
                                   class="bg-blue-600 hover:bg-blue-700 text-white text-center px-4 py-2 rounded-lg text-sm transition">
                                    Lacak Pesanan
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- PAGINATION (jika menggunakan paginate) --}}
            @if(method_exists($orders, 'links'))
                <div class="mt-6">
                    {{ $orders->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <div class="text-5xl mb-4">📭</div>
                <p class="text-gray-400">Belum ada pesanan</p>
                <p class="text-gray-500 text-sm mt-2">Silakan buat pesanan baru untuk memulai</p>
                <a href="{{ route('user.order.select') }}" 
                   class="inline-block mt-4 bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-xl transition">
                    Buat Pesanan
                </a>
            </div>
        @endif

        {{-- BACK BUTTON --}}
        <div class="mt-6 text-center">
            <a href="{{ route('user.home') }}" class="text-sm text-gray-400 hover:text-white transition">
                ← Kembali ke Dashboard
            </a>
        </div>

    </div>
</div>
@endsection