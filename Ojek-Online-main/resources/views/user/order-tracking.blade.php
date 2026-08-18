@extends('layouts.main')

@section('content')
<div class="max-w-2xl mx-auto mt-10 px-4">
    <div class="rounded-2xl bg-gray-900/80 backdrop-blur border border-gray-800 shadow-xl p-8">

        <div class="text-center mb-6">
            <h2 class="text-2xl font-semibold text-white">Status Pesanan</h2>
            <p class="text-sm text-gray-400 mt-1">Pesanan #{{ $pesanan->id }}</p>
        </div>

        @if(session('success'))
            <div class="bg-green-500/20 border border-green-500 text-green-400 rounded-xl p-4 mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-gray-800/50 rounded-xl p-4 mb-6">
            <div class="flex items-center justify-between mb-4">
                <span class="text-gray-400">Status</span>
                <span class="px-3 py-1 rounded-full text-xs font-semibold
                    @if($pesanan->status == 'pending') bg-yellow-500/20 text-yellow-400
                    @elseif($pesanan->status == 'accepted') bg-blue-500/20 text-blue-400
                    @elseif($pesanan->status == 'on_trip') bg-purple-500/20 text-purple-400
                    @elseif($pesanan->status == 'completed') bg-green-500/20 text-green-400
                    @endif">
                    {{ ucfirst(str_replace('_', ' ', $pesanan->status)) }}
                </span>
            </div>

            <div class="border-t border-gray-700 pt-4 space-y-3">
                <div>
                    <p class="text-xs text-gray-500">Titik Jemput</p>
                    <p class="text-white text-sm">{{ $pesanan->pickup_location }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Tujuan</p>
                    <p class="text-white text-sm">{{ $pesanan->destination }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Harga</p>
                    <p class="text-green-400 font-semibold">Rp {{ number_format($pesanan->price, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Metode Pembayaran</p>
                    <p class="text-white text-sm">{{ strtoupper($pesanan->payment_method) }}</p>
                </div>
            </div>
        </div>

        {{-- INFORMASI DRIVER --}}
        @if($pesanan->driver)
        <div class="bg-gray-800/50 rounded-xl p-4 mb-6">
            <h3 class="text-lg font-semibold text-white mb-3">👤 Informasi Driver</h3>
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-500/20 rounded-full flex items-center justify-center text-2xl">
                    🛵
                </div>
                <div>
                    <p class="text-white font-semibold">{{ $pesanan->driver->name ?? '-' }}</p>
                    <p class="text-gray-400 text-sm">Rating: ★ {{ number_format($pesanan->driver->rating ?? 5.0, 1) }}</p>
                    <p class="text-gray-400 text-sm">Plat: {{ $pesanan->driver->vehicle_plate ?? '-' }}</p>
                </div>
            </div>
        </div>

        {{-- PETA LOKASI DRIVER (GOOGLE MAPS) --}}
        <div class="bg-gray-800/50 rounded-xl p-4 mb-6">
            <h3 class="text-lg font-semibold text-white mb-3">📍 Lokasi Driver</h3>
            <div id="driverMap" class="w-full h-[300px] rounded-lg overflow-hidden"></div>
            <p class="text-xs text-gray-500 text-center mt-2" id="driverDistance">Menunggu update lokasi...</p>
        </div>
        @else
        <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-xl p-6 mb-6 text-center">
            <div class="text-4xl mb-2">⏳</div>
            <p class="text-yellow-400">Sedang mencari driver terdekat...</p>
        </div>
        @endif

        <div class="flex gap-3">
            <a href="{{ route('user.home') }}" class="flex-1 bg-gray-700 hover:bg-gray-600 text-white text-center py-3 rounded-xl transition">
                Kembali
            </a>
            @if($pesanan->status == 'completed' && !$pesanan->rating)
            <a href="{{ route('user.order.rating', $pesanan->id) }}" class="flex-1 bg-yellow-600 hover:bg-yellow-700 text-white text-center py-3 rounded-xl transition">
                ⭐ Beri Rating
            </a>
            @endif
        </div>

    </div>
</div>

@if($pesanan->driver)
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAjK4gQ_-XPPBdO4h_0Mz481pY6vpnMkAQ&libraries=places"></script>
<script>
    let driverMarker = null;
    let pickupMarker = null;
    let map = null;
    let watchInterval = null;

    const pickupLocation = { lat: {{ $pesanan->pickup_lat }}, lng: {{ $pesanan->pickup_lng }} };
    const driverId = {{ $pesanan->driver_id }};

    function initMap() {
        map = new google.maps.Map(document.getElementById('driverMap'), {
            center: pickupLocation,
            zoom: 14,
            mapTypeId: google.maps.MapTypeId.roadmap,
        });

        // Marker titik jemput
        pickupMarker = new google.maps.Marker({
            position: pickupLocation,
            map: map,
            label: { text: '📍', color: 'white', fontWeight: 'bold' },
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                fillColor: '#22c55e',
                fillOpacity: 1,
                strokeColor: '#ffffff',
                strokeWeight: 2,
                scale: 14
            },
            title: 'Titik Jemput'
        });
    }

    function updateDriverLocation() {
        fetch('/api/driver/location/' + driverId)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.lat && data.lng) {
                    const driverLocation = { lat: data.lat, lng: data.lng };
                    
                    if (driverMarker) {
                        driverMarker.setPosition(driverLocation);
                    } else {
                        driverMarker = new google.maps.Marker({
                            position: driverLocation,
                            map: map,
                            label: { text: '🛵', color: 'white', fontWeight: 'bold' },
                            icon: {
                                path: google.maps.SymbolPath.CIRCLE,
                                fillColor: '#3b82f6',
                                fillOpacity: 1,
                                strokeColor: '#ffffff',
                                strokeWeight: 2,
                                scale: 12
                            },
                            title: 'Lokasi Driver'
                        });
                    }
                    
                    // Hitung jarak
                    const distance = calculateDistance(
                        driverLocation.lat, driverLocation.lng,
                        pickupLocation.lat, pickupLocation.lng
                    );
                    
                    const distanceText = distance < 1 
                        ? Math.round(distance * 1000) + ' meter' 
                        : distance.toFixed(1) + ' km';
                    
                    document.getElementById('driverDistance').innerHTML = '🚗 Driver berjarak ' + distanceText + ' dari lokasi jemput';
                    
                    // Zoom ke kedua titik
                    const bounds = new google.maps.LatLngBounds();
                    bounds.extend(driverLocation);
                    bounds.extend(pickupLocation);
                    map.fitBounds(bounds);
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

    initMap();
    updateDriverLocation();
    watchInterval = setInterval(updateDriverLocation, 5000);
</script>
@endif
@endsection