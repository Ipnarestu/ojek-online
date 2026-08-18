@extends('layouts.driver')

@section('content')
<div class="max-w-6xl mx-auto mt-10 px-4 space-y-8">

    {{-- ================= HEADER ================= --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-white">
                Dashboard Driver
            </h1>
            <p class="text-gray-400 mt-1">
                Selamat datang, {{ auth()->guard('driver')->user()->name }}
            </p>
        </div>

        {{-- STATUS --}}
        <div class="mt-4 md:mt-0">
            <span class="px-5 py-2 rounded-full text-sm font-semibold
                {{ auth()->guard('driver')->user()->online
                    ? 'bg-green-600/20 text-green-400'
                    : 'bg-gray-600/20 text-gray-400' }}">
                ● {{ auth()->guard('driver')->user()->online ? 'ONLINE' : 'OFFLINE' }}
            </span>
        </div>
    </div>

    {{-- ================= STATUS CARD ================= --}}
    <div class="bg-white/5 backdrop-blur-xl
                border border-white/10
                rounded-2xl shadow-xl p-6
                flex flex-col md:flex-row
                items-center justify-between gap-4">

        <div>
            <h3 class="text-lg font-semibold text-white">
                Status Ketersediaan
            </h3>
            <p class="text-sm text-gray-400 mt-1">
                Aktifkan status online untuk menerima pesanan baru
            </p>
        </div>

        <div class="flex gap-3">
            <button onclick="toggleOnline()"
                    id="toggleOnlineBtn"
                    class="px-6 py-3 rounded-xl font-semibold text-white transition
                    {{ auth()->guard('driver')->user()->online
                        ? 'bg-red-600 hover:bg-red-700'
                        : 'bg-green-600 hover:bg-green-700' }}">
                    {{ auth()->guard('driver')->user()->online ? 'Go Offline' : 'Go Online' }}
            </button>

            <a href="{{ route('driver.orders') }}"
               class="px-6 py-3 rounded-xl bg-blue-600 text-white
                      font-semibold hover:bg-blue-700 transition">
                Pesanan Masuk
            </a>
        </div>
    </div>

    {{-- ================= STATISTIK (dengan ID untuk update real-time) ================= --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-4 text-center">
            <p class="text-2xl font-bold text-green-400" id="todayEarnings">Rp {{ number_format($todayEarnings ?? 0, 0, ',', '.') }}</p>
            <p class="text-gray-400 text-sm">Pendapatan Hari Ini (92%)</p>
        </div>
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-4 text-center">
            <p class="text-2xl font-bold text-white" id="totalOrders">{{ $totalOrders ?? 0 }}</p>
            <p class="text-gray-400 text-sm">Total Pesanan Selesai</p>
        </div>
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-4 text-center">
            <p class="text-2xl font-bold text-yellow-400">{{ number_format($driver->rating ?? 5.0, 1) }} ★</p>
            <p class="text-gray-400 text-sm">Rating Driver</p>
        </div>
    </div>

    {{-- ================= DETAIL PENDAPATAN HARI INI ================= --}}
    @if(isset($todayStats) && $todayStats->daily_orders_count > 0)
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-5">
        <h3 class="text-white font-semibold mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            Detail Pendapatan Hari Ini
        </h3>
        <div class="space-y-3">
            <div class="flex justify-between items-center pb-2 border-b border-gray-700">
                <span class="text-gray-400">Pesanan Selesai</span>
                <span class="text-white font-bold">{{ $todayStats->daily_orders_count }} pesanan</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-400"> Pendapatan Kotor</span>
                <span class="text-white">Rp {{ number_format(($todayStats->daily_total_earnings ?? 0) + ($todayStats->daily_admin_fee ?? 0), 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-400"> Potongan Admin (8%)</span>
                <span class="text-red-400">- Rp {{ number_format($todayStats->daily_admin_fee ?? 0, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center pt-2 border-t border-gray-700">
                <span class="text-gray-400 font-semibold">✅ Pendapatan Bersih Driver (92%)</span>
                <span class="text-green-400 font-bold text-lg" id="detailTodayEarnings">Rp {{ number_format($todayStats->daily_total_earnings ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>
        <div class="mt-3 text-xs text-gray-500 bg-gray-800/30 rounded-lg p-2 text-center">
            💡 Tips: Semakin banyak pesanan, pendapatan Anda semakin bertambah!
        </div>
    </div>
    @endif

    {{-- ================= PESANAN AKTIF ================= --}}
    @if(isset($activeOrder) && $activeOrder)
    <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-2xl p-6">
        <h3 class="text-yellow-400 font-semibold mb-3 flex items-center gap-2">
            <span class="text-xl">🔄</span> Pesanan Aktif
        </h3>
        <p class="text-white"> Jemput: {{ $activeOrder->pickup_location }}</p>
        <p class="text-white"> Tujuan: {{ $activeOrder->destination }}</p>
        <p class="text-sm text-gray-400 mt-2"> Harga: Rp {{ number_format($activeOrder->price, 0, ',', '.') }}</p>
        <div class="mt-3 text-xs text-gray-500">
             Setelah selesai, Anda akan mendapatkan 92% dari harga pesanan
        </div>
        <a href="{{ route('driver.orders') }}" class="inline-block mt-3 text-green-400 hover:underline">Lihat detail →</a>
    </div>
    @endif

    {{-- ================= VEHICLE CARD ================= --}}
    <div class="bg-white/5 backdrop-blur-xl
                border border-white/10
                rounded-2xl shadow-xl p-6">

        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-white">
                Kendaraan Terdaftar
            </h3>

            <button onclick="toggleForm()"
                class="text-sm text-blue-400 hover:underline">
                {{ auth()->guard('driver')->user()->kendaraan ? 'Ubah Data' : 'Tambah Kendaraan' }}
            </button>
        </div>

        {{-- DISPLAY --}}
        @if(auth()->guard('driver')->user()->kendaraan)
            <div id="vehicleInfo" class="space-y-1 text-gray-200">
                <p class="font-semibold">
                    {{ auth()->guard('driver')->user()->kendaraan->Tipe }}
                </p>
                <p class="text-sm text-gray-400">
                    {{ auth()->guard('driver')->user()->kendaraan->Merk }}
                    · {{ auth()->guard('driver')->user()->kendaraan->Warna }}
                </p>
                <p class="text-xs text-gray-500">
                    Plat Nomor: {{ auth()->guard('driver')->user()->kendaraan->Plat_Nomor }}
                </p>
            </div>
        @else
            <p class="text-sm text-gray-500">
                Data kendaraan belum diisi.
            </p>
        @endif

        {{-- FORM --}}
        <div id="vehicleForm" class="hidden mt-6">
            <form method="POST"
                  action="{{ route('driver.kendaraan.save') }}"
                  class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                @csrf

                <input name="Plat_Nomor" placeholder="Plat Nomor"
                       value="{{ old('Plat_Nomor', auth()->guard('driver')->user()->kendaraan->Plat_Nomor ?? '') }}"
                       class="bg-gray-900 border border-white/10
                              rounded-xl px-4 py-3 text-white"
                       required>

                <select name="Tipe"
                        class="bg-gray-900 border border-white/10
                               rounded-xl px-4 py-3 text-white"
                        required>
                    <option value="">Tipe Kendaraan</option>
                    <option value="Motor">Motor</option>
                    <option value="Mobil">Mobil</option>
                </select>

                <input name="Merk" placeholder="Merk"
                       value="{{ old('Merk', auth()->guard('driver')->user()->kendaraan->Merk ?? '') }}"
                       class="bg-gray-900 border border-white/10
                              rounded-xl px-4 py-3 text-white"
                       required>

                <input name="Warna" placeholder="Warna"
                       value="{{ old('Warna', auth()->guard('driver')->user()->kendaraan->Warna ?? '') }}"
                       class="bg-gray-900 border border-white/10
                              rounded-xl px-4 py-3 text-white"
                       required>

                <div class="md:col-span-2 flex gap-3">
                    <button class="bg-blue-600 px-6 py-3 rounded-xl text-white font-semibold">
                        Simpan
                    </button>
                    <button type="button" onclick="toggleForm()"
                        class="text-gray-400 hover:underline">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

{{-- ================= NOTIF PESANAN BARU ================= --}}
@include('driver.partials.order-notification')

<script>
function toggleForm() {
    document.getElementById('vehicleForm').classList.toggle('hidden');
    document.getElementById('vehicleInfo')?.classList.toggle('hidden');
}

function toggleOnline() {
    fetch('{{ route("driver.toggle.online") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({})
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function updateLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            fetch('{{ route("driver.update.location") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                })
            });
        });
    }
}

// ========================
// REAL-TIME UPDATE DASHBOARD
// ========================
function updateDashboardStats() {
    fetch('{{ route("driver.get.stats") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update Pendapatan Hari Ini
                const earningsElement = document.getElementById('todayEarnings');
                if (earningsElement) {
                    earningsElement.innerHTML = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.today_earnings);
                }
                
                // Update Total Pesanan
                const ordersElement = document.getElementById('totalOrders');
                if (ordersElement) {
                    ordersElement.innerHTML = data.total_orders;
                }
                
                // Update detail pendapatan jika ada
                const detailEarnings = document.getElementById('detailTodayEarnings');
                if (detailEarnings) {
                    detailEarnings.innerHTML = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.today_earnings);
                }
                
                console.log('Dashboard updated:', data);
            }
        })
        .catch(error => console.error('Error updating stats:', error));
}

// Cek apakah ada update dari halaman detail (setelah selesai pesanan)
if (localStorage.getItem('dashboard_updated') === 'true') {
    updateDashboardStats();
    localStorage.removeItem('dashboard_updated');
}

// Update setiap 30 detik sebagai fallback
setInterval(updateDashboardStats, 30000);

// Update location setiap 10 detik
setInterval(updateLocation, 10000);
updateLocation();
</script>
@endsection