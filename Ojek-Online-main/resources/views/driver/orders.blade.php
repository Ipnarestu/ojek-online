@extends('layouts.driver')

@section('content')
<div class="max-w-6xl mx-auto mt-10 px-4 space-y-8">

    {{-- HEADER --}}
    <div>
        <h2 class="text-3xl font-bold text-white">
            Pesanan Masuk
        </h2>
        <p class="text-gray-400 mt-1">
            Pesanan yang tersedia dan sedang kamu tangani
        </p>
    </div>

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div class="bg-green-500/20 border border-green-500/30 text-green-300 px-4 py-3 rounded-xl text-sm">
             {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-500/20 border border-red-500/30 text-red-300 px-4 py-3 rounded-xl text-sm">
             {{ session('error') }}
        </div>
    @endif

    {{-- ================= PESANAN AKTIF ================= --}}
    @if(isset($activeOrders) && $activeOrders->count() > 0)
    <div class="bg-blue-500/10 border border-blue-500/30 rounded-2xl p-6">
        <h3 class="text-lg font-semibold text-blue-400 mb-4 flex items-center gap-2">
             Pesanan Aktif ({{ $activeOrders->count() }})
        </h3>
        <div class="space-y-4">
            @foreach($activeOrders as $order)
            <div class="bg-gray-800 rounded-xl p-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <p class="text-white font-semibold">Order #{{ $order->id }}</p>
                        <p class="text-gray-400 text-sm">Jemput: {{ Str::limit($order->pickup_location, 40) }}</p>
                        <p class="text-gray-400 text-sm">Tujuan: {{ Str::limit($order->destination, 40) }}</p>
                        <p class="text-green-400 text-sm mt-1">💰 Rp {{ number_format($order->price, 0, ',', '.') }}</p>
                        <p class="text-gray-500 text-xs mt-1">
                            Status: 
                            @if($order->status === 'accepted')
                                <span class="text-yellow-400">● Menuju lokasi jemput</span>
                            @elseif($order->status === 'on_trip')
                                <span class="text-purple-400">● Sedang mengantar ke tujuan</span>
                            @endif
                        </p>
                    </div>
                    <a href="{{ route('driver.order.detail', $order->id) }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm text-center whitespace-nowrap">
                         Lihat Detail
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ================= PESANAN TERSEdIA ================= --}}
    <div class="bg-slate-800/70 backdrop-blur border border-white/10 rounded-2xl shadow-xl p-6">
        <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
             Pesanan Tersedia ({{ isset($availableOrders) ? $availableOrders->count() : 0 }})
        </h3>

        @if(isset($availableOrders) && $availableOrders->count() > 0)
        <div class="space-y-4">
            @foreach($availableOrders as $order)
            <div class="bg-gray-800/50 rounded-xl p-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <p class="text-white font-semibold">Order #{{ $order->id }}</p>
                        <p class="text-gray-400 text-sm"> Jemput: {{ Str::limit($order->pickup_location, 40) }}</p>
                        <p class="text-gray-400 text-sm"> Tujuan: {{ Str::limit($order->destination, 40) }}</p>
                        <p class="text-green-400 text-sm mt-1"> Rp {{ number_format($order->price, 0, ',', '.') }}</p>
                        <p class="text-gray-500 text-xs mt-1"> {{ $order->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex gap-2">
                        <form action="{{ route('driver.orders.accept', $order->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg text-sm font-semibold cursor-pointer">
                                 Terima
                            </button>
                        </form>
                        <form action="{{ route('driver.orders.reject', $order->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg text-sm font-semibold cursor-pointer">
                                 Tolak
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-8">
            <div class="text-5xl mb-3"></div>
            <p class="text-gray-400">Belum ada pesanan masuk saat ini</p>
            <p class="text-gray-500 text-sm mt-1">Pastikan status Anda Online</p>
            <a href="{{ route('driver.dashboard') }}" class="inline-block mt-4 px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold transition">
                Kembali ke Dashboard
            </a>
        </div>
        @endif
    </div>

</div>
@endsection