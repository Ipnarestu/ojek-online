@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8">

    {{-- PAGE HEADER --}}
    <div class="mb-10">
        <h1 class="text-2xl font-semibold text-white">
            Dashboard Admin
        </h1>
        <p class="text-sm text-gray-400 mt-1">
            Ringkasan aktivitas sistem secara real-time
        </p>
    </div>

    {{-- KPI CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-12">

        {{-- USERS --}}
        <div class="bg-white/5 backdrop-blur border border-white/10 rounded-xl p-6">
            <p class="text-sm text-gray-400">Total Users</p>
            <p class="text-3xl font-bold text-white mt-1">
                {{ number_format($totalUsers ?? 0) }}
            </p>
            <p class="text-xs text-green-400 mt-2">
                Aktif: {{ number_format($activeUsers ?? 0) }}
            </p>
        </div>

        {{-- DRIVERS --}}
        <div class="bg-white/5 backdrop-blur border border-white/10 rounded-xl p-6">
            <p class="text-sm text-gray-400">Total Drivers</p>
            <p class="text-3xl font-bold text-white mt-1">
                {{ number_format($totalDrivers ?? 0) }}
            </p>
            <p class="text-xs text-blue-400 mt-2">
                Online: {{ number_format($onlineDrivers ?? 0) }}
            </p>
        </div>

        {{-- ORDERS --}}
        <div class="bg-white/5 backdrop-blur border border-white/10 rounded-xl p-6">
            <p class="text-sm text-gray-400">Total Orders</p>
            <p class="text-3xl font-bold text-white mt-1">
                {{ number_format($totalOrders ?? 0) }}
            </p>
            <p class="text-xs text-green-400 mt-2">
                Completed: {{ number_format($completedOrders ?? 0) }}
            </p>
        </div>

        {{-- PENDAPATAN KOMISI ADMIN (8%) --}}
        <div class="bg-white/5 backdrop-blur border border-white/10 rounded-xl p-6">
            <p class="text-sm text-gray-400">Pendapatan Admin (8%)</p>
            <p class="text-3xl font-bold text-purple-400 mt-1">
                Rp {{ number_format($totalAdminCommission ?? 0, 0, ',', '.') }}
            </p>
            <p class="text-xs text-gray-500 mt-2">
                Hari ini: Rp {{ number_format($todayAdminCommission ?? 0, 0, ',', '.') }}
            </p>
        </div>

        {{-- TOTAL PENDAPATAN KOTOR --}}
        <div class="bg-white/5 backdrop-blur border border-white/10 rounded-xl p-6">
            <p class="text-sm text-gray-400">Total Pendapatan Kotor</p>
            <p class="text-3xl font-bold text-white mt-1">
                Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}
            </p>
            <p class="text-xs text-gray-500 mt-2">
                Driver: Rp {{ number_format($totalDriverEarnings ?? 0, 0, ',', '.') }}
            </p>
        </div>

    </div>

    {{-- CHART SECTION --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">

        {{-- ORDER STATUS --}}
        <div class="bg-white/5 backdrop-blur border border-white/10 rounded-xl p-6">
            <h3 class="font-medium text-gray-200 mb-4">
                Status Pesanan
            </h3>
            <div class="flex justify-center">
                <canvas id="orderChart" class="max-w-[260px] max-h-[260px]"></canvas>
            </div>
        </div>

        {{-- DRIVER STATUS --}}
        <div class="bg-white/5 backdrop-blur border border-white/10 rounded-xl p-6">
            <h3 class="font-medium text-gray-200 mb-4">
                Status Driver
            </h3>
            <div class="flex justify-center">
                <canvas id="driverChart" class="max-w-[260px] max-h-[260px]"></canvas>
            </div>
        </div>

    </div>

    {{-- TOP INSIGHTS (AMAN DARI NULL) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        
        {{-- TOP RATING DRIVER --}}
        <div class="bg-white/5 backdrop-blur border border-white/10 rounded-xl p-5">
            <h3 class="font-medium text-gray-200 mb-4">🏆 Top Rating Driver</h3>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white font-semibold text-lg">
                        {{ optional($topRatedDriver)->name ?? '-' }}
                    </p>
                    <p class="text-yellow-400 text-sm">
                        ★ {{ optional($topRatedDriver)->avg_rating ?? 0 }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-yellow-500/20 rounded-full flex items-center justify-center text-2xl">
                    
                </div>
            </div>
        </div>

        {{-- MOST ACTIVE DRIVER --}}
        <div class="bg-white/5 backdrop-blur border border-white/10 rounded-xl p-5">
            <h3 class="font-medium text-gray-200 mb-4">📊 Driver Paling Aktif</h3>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white font-semibold text-lg">
                        {{ optional($mostActiveDriver)->name ?? '-' }}
                    </p>
                    <p class="text-gray-400 text-sm">
                        {{ optional($mostActiveDriver)->total_orders ?? 0 }} pesanan
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-500/20 rounded-full flex items-center justify-center text-2xl">
                    📦
                </div>
            </div>
        </div>

        {{-- TOP EARNING DRIVER --}}
        <div class="bg-white/5 backdrop-blur border border-white/10 rounded-xl p-5">
            <h3 class="font-medium text-gray-200 mb-4">💰 Top Earning Driver</h3>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white font-semibold text-lg">
                        {{ optional($topEarningDriverData)->name ?? '-' }}
                    </p>
                    <p class="text-green-400 text-sm">
                        {{ optional($topEarningDriverData)->total_earning ? 'Rp ' . number_format($topEarningDriverData->total_earning, 0, ',', '.') : 'Rp 0' }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-500/20 rounded-full flex items-center justify-center text-2xl">
                    💰
                </div>
            </div>
        </div>

    </div>

    {{-- RECENT ACTIVITY --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">

        {{-- RECENT ORDERS --}}
        <div class="bg-white/5 backdrop-blur border border-white/10 rounded-xl p-5">
            <h3 class="font-medium text-gray-200 mb-4">
                📋 Pesanan Terbaru
            </h3>
            @forelse(($recentOrders ?? []) as $order)
                <div class="border-b border-white/10 py-2 text-sm">
                    <div class="text-white font-medium">
                        Order #{{ $order->id }}
                    </div>
                    <div class="text-xs text-gray-400">
                        Status: {{ strtoupper($order->status) }}
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">Belum ada pesanan</p>
            @endforelse
        </div>

        {{-- RECENT USERS --}}
        <div class="bg-white/5 backdrop-blur border border-white/10 rounded-xl p-5">
            <h3 class="font-medium text-gray-200 mb-4">
                👤 User Terbaru
            </h3>
            @forelse(($recentUsers ?? []) as $user)
                <div class="border-b border-white/10 py-2 text-sm">
                    <div class="text-white font-medium">
                        {{ $user->name }}
                    </div>
                    <div class="text-xs text-gray-400">
                        {{ $user->created_at->diffForHumans() }}
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">Belum ada user</p>
            @endforelse
        </div>

        {{-- RECENT DRIVERS --}}
        <div class="bg-white/5 backdrop-blur border border-white/10 rounded-xl p-5">
            <h3 class="font-medium text-gray-200 mb-4">
                 Driver Terbaru
            </h3>
            @forelse(($recentDrivers ?? []) as $driver)
                <div class="border-b border-white/10 py-2 text-sm">
                    <div class="text-white font-medium">
                        {{ $driver->name }}
                    </div>
                    <div class="text-xs text-gray-400">
                        {{ $driver->created_at->diffForHumans() }}
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">Belum ada driver</p>
            @endforelse
        </div>

    </div>

    {{-- QUICK LINKS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('admin.users.index') }}" 
           class="bg-blue-600/20 hover:bg-blue-600/30 border border-blue-500/30 rounded-xl p-4 text-center transition group">
            <p class="text-blue-400 font-semibold group-hover:text-blue-300"> Kelola User</p>
            <p class="text-xs text-gray-400 mt-1">Lihat dan kelola semua user</p>
        </a>
        <a href="{{ route('admin.drivers.index') }}" 
           class="bg-green-600/20 hover:bg-green-600/30 border border-green-500/30 rounded-xl p-4 text-center transition group">
            <p class="text-green-400 font-semibold group-hover:text-green-300"> Kelola Driver</p>
            <p class="text-xs text-gray-400 mt-1">Lihat dan kelola semua driver</p>
        </a>
        <a href="{{ route('admin.orders.index') }}" 
           class="bg-purple-600/20 hover:bg-purple-600/30 border border-purple-500/30 rounded-xl p-4 text-center transition group">
            <p class="text-purple-400 font-semibold group-hover:text-purple-300"> Kelola Pesanan</p>
            <p class="text-xs text-gray-400 mt-1">Lihat dan kelola semua pesanan</p>
        </a>
    </div>

</div>

{{-- CHART JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    Chart.defaults.color = '#cbd5f5';

    // Order Status Chart
    new Chart(document.getElementById('orderChart'), {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Accepted', 'Completed'],
            datasets: [{
                data: [
                    {{ $orderPending ?? 0 }},
                    {{ $orderAccepted ?? 0 }},
                    {{ $orderCompleted ?? 0 }}
                ],
                backgroundColor: ['#f59e0b', '#3b82f6', '#22c55e'],
                borderWidth: 0
            }]
        },
        options: {
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // Driver Status Chart
    new Chart(document.getElementById('driverChart'), {
        type: 'pie',
        data: {
            labels: ['Online', 'Offline'],
            datasets: [{
                data: [
                    {{ $onlineDrivers ?? 0 }},
                    {{ $offlineDrivers ?? 0 }}
                ],
                backgroundColor: ['#22c55e', '#ef4444'],
                borderWidth: 0
            }]
        },
        options: {
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>
@endsection