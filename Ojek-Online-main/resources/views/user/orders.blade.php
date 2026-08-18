@extends('layouts.driver')

@section('title', 'Pesanan - Driver Panel')

@section('content')
<div class="max-w-6xl mx-auto mt-8 px-4 space-y-8">

    {{-- HEADER --}}
    <div>
        <h2 class="text-3xl font-bold text-white">
            📋 Manajemen Pesanan
        </h2>
        <p class="text-gray-400 mt-1">
            Kelola pesanan yang sedang berlangsung dan pesanan baru
        </p>
    </div>

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div class="bg-green-500/20 border border-green-500/30 text-green-300 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
            <span>✅</span> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-500/20 border border-red-500/30 text-red-300 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
            <span>❌</span> {{ session('error') }}
        </div>
    @endif

    @if(session('warning'))
        <div class="bg-yellow-500/20 border border-yellow-500/30 text-yellow-300 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
            <span>⚠️</span> {{ session('warning') }}
        </div>
    @endif

    {{-- ================= PESANAN AKTIF ================= --}}
    @if(isset($activeOrders) && $activeOrders->count() > 0)
    <div class="bg-blue-500/10 border border-blue-500/30 rounded-2xl p-6">
        <h3 class="text-lg font-semibold text-blue-400 mb-4 flex items-center gap-2">
            <span>🔄</span> Pesanan Aktif ({{ $activeOrders->count() }})
        </h3>
        <div class="space-y-4">
            @foreach($activeOrders as $order)
            <div class="bg-gray-800/70 rounded-xl p-4 border border-blue-500/20">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-white font-semibold text-lg">Order #{{ $order->id }}</span>
                            @if($order->status === 'accepted')
                                <span class="bg-yellow-500/20 text-yellow-400 text-xs px-2 py-1 rounded-full">Menuju Lokasi Jemput</span>
                            @elseif($order->status === 'on_trip')
                                <span class="bg-purple-500/20 text-purple-400 text-xs px-2 py-1 rounded-full">Sedang Mengantar</span>
                            @endif
                        </div>
                        <p class="text-gray-400 text-sm flex items-center gap-2">
                            <span>📍</span> Jemput: {{ Str::limit($order->pickup_location, 50) }}
                        </p>
                        <p class="text-gray-400 text-sm flex items-center gap-2 mt-1">
                            <span>🎯</span> Tujuan: {{ Str::limit($order->destination, 50) }}
                        </p>
                        <p class="text-green-400 text-sm mt-2 font-semibold">
                            💰 Rp {{ number_format($order->price, 0, ',', '.') }}
                            <span class="text-gray-500 text-xs font-normal ml-2">
                                (Komisi 8%: -Rp {{ number_format($order->price * 0.08, 0, ',', '.') }})
                            </span>
                        </p>
                        <p class="text-gray-500 text-xs mt-1">
                            👤 Pelanggan: {{ $order->user->name ?? 'Unknown' }}
                        </p>
                    </div>
                    <a href="{{ route('driver.order.detail', $order->id) }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold text-center whitespace-nowrap transition flex items-center gap-2 justify-center">
                        🗺️ Lihat Detail & Rute
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
            <span>🆕</span> Pesanan Tersedia 
            <span class="bg-green-500/20 text-green-400 text-xs px-2 py-1 rounded-full">
                {{ isset($availableOrders) ? $availableOrders->count() : 0 }}
            </span>
        </h3>

        @if(isset($availableOrders) && $availableOrders->count() > 0)
        <div class="space-y-4">
            @foreach($availableOrders as $order)
            <div class="bg-gray-800/50 rounded-xl p-4 hover:bg-gray-800/70 transition border border-gray-700">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-white font-semibold">Order #{{ $order->id }}</span>
                            <span class="bg-green-500/20 text-green-400 text-xs px-2 py-0.5 rounded-full">Baru</span>
                            <span class="text-gray-500 text-xs">{{ $order->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-gray-400 text-sm flex items-center gap-2">
                            <span>📍</span> {{ Str::limit($order->pickup_location, 45) }}
                        </p>
                        <p class="text-gray-400 text-sm flex items-center gap-2 mt-1">
                            <span>🎯</span> {{ Str::limit($order->destination, 45) }}
                        </p>
                        <div class="flex items-center gap-3 mt-2">
                            <p class="text-green-400 text-sm font-semibold">
                                💰 Rp {{ number_format($order->price, 0, ',', '.') }}
                            </p>
                            <p class="text-gray-500 text-xs">
                                🚗 Jarak: {{ round($order->distance ?? 2.5, 1) }} km
                            </p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <form action="{{ route('driver.orders.accept', $order->id) }}" method="POST" class="accept-form" data-order-id="{{ $order->id }}">
                            @csrf
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold cursor-pointer transition flex items-center gap-1">
                                ✅ Terima
                            </button>
                        </form>
                        <form action="{{ route('driver.orders.reject', $order->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-red-600/50 hover:bg-red-600 text-white px-5 py-2.5 rounded-xl text-sm font-semibold cursor-pointer transition">
                                ✖ Tolak
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-12">
            <div class="text-6xl mb-4">📭</div>
            <p class="text-gray-400 text-lg">Belum ada pesanan masuk saat ini</p>
            <p class="text-gray-500 text-sm mt-2">Pastikan status Anda <span class="text-green-400">Online</span> untuk mendapatkan pesanan</p>
            
            <div class="mt-6 flex gap-3 justify-center">
                <a href="{{ route('driver.dashboard') }}" 
                   class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold transition">
                    Kembali ke Dashboard
                </a>
                <button onclick="location.reload()" 
                        class="px-6 py-2.5 rounded-xl bg-gray-700 hover:bg-gray-600 text-white font-semibold transition">
                    🔄 Refresh
                </button>
            </div>
        </div>
        @endif
    </div>
    
    {{-- Informasi status driver --}}
    <div class="bg-slate-800/50 rounded-xl p-4 text-center text-sm text-gray-500">
        <p>
            💡 Sistem akan memberikan pesanan secara otomatis ke driver yang tersedia.
            Pastikan koneksi internet stabil untuk menerima notifikasi pesanan baru.
        </p>
    </div>

</div>

{{-- 🔥 SCRIPT UNTUK HANDLE ACCEPT DENGAN LOADING STATE --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle form accept dengan loading state
        const acceptForms = document.querySelectorAll('.accept-form');
        
        acceptForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const button = this.querySelector('button[type="submit"]');
                const originalText = button.innerHTML;
                
                button.innerHTML = '⏳ Memproses...';
                button.disabled = true;
                
                // Simpan original text untuk restore jika error
                setTimeout(() => {
                    if (button.disabled) {
                        button.innerHTML = originalText;
                        button.disabled = false;
                    }
                }, 10000);
            });
        });
        
        // Auto refresh setiap 30 detik untuk update daftar pesanan
        let refreshInterval = setInterval(function() {
            // Hanya refresh jika tidak ada modal terbuka dan tidak ada form sedang diproses
            const modal = document.getElementById('notificationModal');
            const isModalOpen = modal && !modal.classList.contains('hidden');
            const isProcessing = document.querySelector('button[disabled]');
            
            if (!isModalOpen && !isProcessing) {
                fetch(window.location.href)
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newContent = doc.querySelector('.max-w-6xl.mx-auto');
                        const currentContent = document.querySelector('.max-w-6xl.mx-auto');
                        if (newContent && currentContent) {
                            currentContent.innerHTML = newContent.innerHTML;
                        }
                    })
                    .catch(err => console.log('Refresh error:', err));
            }
        }, 30000);
        
        // Cleanup interval saat page leave
        window.addEventListener('beforeunload', function() {
            if (refreshInterval) clearInterval(refreshInterval);
        });
    });
</script>

@endsection