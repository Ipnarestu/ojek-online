@extends('layouts.admin')

@section('content')
<div class="space-y-6">

    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-white">Detail User</h1>
            <p class="text-gray-400 mt-1">{{ $user->name }}</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="text-gray-400 hover:text-white">← Kembali</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- INFORMASI USER --}}
        <div class="lg:col-span-1 bg-gray-800/50 rounded-xl p-6 border border-gray-700">
            <div class="text-center mb-4">
                <div class="w-24 h-24 mx-auto bg-gray-700 rounded-full flex items-center justify-center text-4xl">
                    👤
                </div>
                <h2 class="text-xl font-bold text-white mt-3">{{ $user->name }}</h2>
                <p class="text-gray-400 text-sm">{{ $user->email }}</p>
            </div>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-400">Telepon</span>
                    <span class="text-white">{{ $user->phone ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Bergabung</span>
                    <span class="text-white">{{ $user->created_at->format('d M Y, H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Total Pesanan</span>
                    <span class="text-white">{{ $totalOrders }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Total Belanja</span>
                    <span class="text-green-400">Rp {{ number_format($totalSpent, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- RIWAYAT PESANAN --}}
        <div class="lg:col-span-2 bg-gray-800/50 rounded-xl p-6 border border-gray-700">
            <h3 class="text-lg font-semibold text-white mb-4">📋 Riwayat Pesanan</h3>
            
            @if($user->pesanans->count() > 0)
            <div class="space-y-3">
                @foreach($user->pesanans as $order)
                <div class="bg-gray-700/30 rounded-lg p-3">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-white font-medium">Order #{{ $order->id }}</p>
                            <p class="text-sm text-gray-400">{{ $order->pickup_location }} → {{ $order->destination }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $order->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-green-400 font-semibold">Rp {{ number_format($order->price, 0, ',', '.') }}</p>
                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                @if($order->status == 'pending') bg-yellow-500/20 text-yellow-400
                                @elseif($order->status == 'completed') bg-green-500/20 text-green-400
                                @else bg-gray-500/20 text-gray-400
                                @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-400 text-center py-4">Belum ada pesanan</p>
            @endif
        </div>

    </div>

</div>
@endsection