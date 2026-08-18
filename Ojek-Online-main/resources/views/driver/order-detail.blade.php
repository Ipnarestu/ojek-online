@extends('layouts.driver')

@section('content')
<div class="max-w-4xl mx-auto mt-10 px-4">
    <div class="bg-gray-900/80 backdrop-blur border border-gray-800 rounded-2xl p-8">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-white">Detail Pesanan #{{ $order->id }}</h1>
            <a href="{{ route('driver.orders') }}" class="text-gray-400 hover:text-white">← Kembali</a>
        </div>

        {{-- STATUS --}}
        <div class="bg-gray-800/50 rounded-xl p-4 mb-6">
            <div class="flex items-center justify-between">
                <span class="text-gray-400">Status</span>
                <span class="px-3 py-1 rounded-full text-xs font-semibold
                    @if($order->status === 'accepted') bg-yellow-500/20 text-yellow-400
                    @elseif($order->status === 'on_trip') bg-purple-500/20 text-purple-400
                    @elseif($order->status === 'completed') bg-green-500/20 text-green-400
                    @else bg-gray-500/20 text-gray-400
                    @endif">
                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                </span>
            </div>
        </div>

        {{-- INFORMASI PESANAN --}}
        <div class="bg-gray-800/50 rounded-xl p-4 mb-6 space-y-3">
            <div>
                <p class="text-xs text-gray-500"> Titik Jemput</p>
                <p class="text-white">{{ $order->pickup_location }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500"> Tujuan</p>
                <p class="text-white">{{ $order->destination }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500"> Harga Pesanan</p>
                <p class="text-green-400 font-semibold">Rp {{ number_format($order->price, 0, ',', '.') }}</p>
            </div>
            <div class="border-t border-gray-700 pt-2 mt-2">
                <p class="text-xs text-gray-500"> Rincian Pendapatan Driver</p>
                <div class="flex justify-between text-sm mt-1">
                    <span class="text-gray-400">Harga Pesanan (100%)</span>
                    <span class="text-white">Rp {{ number_format($order->price, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">Potongan Admin (8%)</span>
                    <span class="text-red-400">- Rp {{ number_format($order->admin_fee ?? $order->price * 0.08, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm font-semibold pt-1 border-t border-gray-700 mt-1">
                    <span class="text-green-400">Pendapatan Bersih Driver (92%)</span>
                    <span class="text-green-400">Rp {{ number_format($order->driver_earning ?? $order->price * 0.92, 0, ',', '.') }}</span>
                </div>
            </div>
            <div>
                <p class="text-xs text-gray-500">💳 Metode Pembayaran</p>
                <p class="text-white">{{ strtoupper($order->payment_method) }}</p>
            </div>
            @if($order->pickup_note)
            <div>
                <p class="text-xs text-gray-500"> Catatan</p>
                <p class="text-white">{{ $order->pickup_note }}</p>
            </div>
            @endif
        </div>

        {{-- INFORMASI USER --}}
        <div class="bg-gray-800/50 rounded-xl p-4 mb-6">
            <h3 class="text-lg font-semibold text-white mb-3"> Informasi Pemesan</h3>
            <p class="text-white">Nama: {{ $order->user->name ?? '-' }}</p>
            <p class="text-gray-400">Email: {{ $order->user->email ?? '-' }}</p>
            <p class="text-gray-400">Telepon: {{ $order->user->phone ?? '-' }}</p>
        </div>

        {{-- ACTION BUTTONS --}}
        <div class="flex gap-3">
            @if($order->status === 'accepted')
                <a href="{{ route('driver.order.pickup', $order->id) }}" 
                   class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 rounded-xl transition text-center">
                     Jemput Penumpang
                </a>
            @endif

            @if($order->status === 'on_trip')
                <a href="{{ route('driver.order.delivery', $order->id) }}" 
                   class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 rounded-xl transition text-center">
                     Antar Penumpang
                </a>

                {{-- FORM SUBMIT BIASA (TANPA AJAX) --}}
                <form action="{{ route('driver.orders.complete', $order->id) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl transition">
                         Selesaikan Pesanan
                    </button>
                </form>
            @endif
        </div>

        @if($order->status === 'completed')
            <div class="text-center mt-6 text-green-400">
                ✔ Pesanan sudah selesai. Terima kasih!
            </div>
        @endif

    </div>
</div>
@endsection