@extends('layouts.driver')

@section('content')
<div class="max-w-6xl mx-auto px-4 mt-10">

    {{-- HEADER --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-white">
             Jemput Penumpang
        </h2>
        <p class="text-sm text-gray-400 mt-1">
            Navigasi ke lokasi jemput penumpang
        </p>
    </div>

    {{-- MAP CARD --}}
    <div class="bg-gray-800/50 backdrop-blur rounded-2xl overflow-hidden border border-gray-700">
        <div id="map" class="w-full h-[400px]"></div>
    </div>

    {{-- INFO PERJALANAN (Jemput di atas besar, Tujuan di bawah kecil) --}}
    <div class="mt-4 bg-gray-800/50 rounded-xl p-4">
        {{-- LOKASI JEMPUT (BESAR) --}}
        <div class="mb-4 p-3 bg-green-900/30 rounded-lg border border-green-700/30">
            <p class="text-xs text-green-400 uppercase tracking-wide">📍 Titik Jemput</p>
            <p class="text-white font-semibold text-lg">{{ $order->pickup_location }}</p>
        </div>

        {{-- LOKASI TUJUAN (KECIL/GELAP) --}}
        <div class="p-3 bg-gray-800/50 rounded-lg">
            <p class="text-xs text-gray-500 uppercase tracking-wide">🎯 Tujuan</p>
            <p class="text-gray-400 text-sm">{{ $order->destination }}</p>
        </div>
    </div>

    {{-- INFORMASI PEMESAN --}}
    <div class="mt-4 bg-gray-800/30 rounded-xl p-3">
        <p class="text-xs text-gray-400">👤 Pemesan: <span class="text-white">{{ $order->user->name ?? '-' }}</span></p>
    </div>

    {{-- TOMBOL --}}
    <div class="mt-6 flex gap-3">
        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $order->pickup_lat }},{{ $order->pickup_lng }}&travelmode=driving"
           target="_blank"
           class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-xl transition text-center">
            🗺️ Navigasi ke Lokasi Jemput
        </a>

        {{-- TOMBOL SELESAI JEMPUT --}}
        <form action="{{ route('driver.pickup.complete', $order->id) }}" method="POST" class="flex-1">
            @csrf
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl transition">
                 Selesai Jemput
            </button>
        </form>

        <a href="{{ route('driver.order.detail', $order->id) }}"
           class="px-6 py-3 rounded-xl border border-gray-600 text-gray-300 hover:bg-gray-700 transition">
            ← Kembali
        </a>
    </div>

</div>

{{-- GOOGLE MAPS SCRIPT --}}
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAjK4gQ_-XPPBdO4h_0Mz481pY6vpnMkAQ&libraries=places&callback=initMap" async defer></script>

<script>
    // Koordinat dari server
    const pickupLat = {{ $order->pickup_lat }};
    const pickupLng = {{ $order->pickup_lng }};

    let map;

    function initMap() {
        const pickupLocation = { lat: pickupLat, lng: pickupLng };
        
        map = new google.maps.Map(document.getElementById('map'), {
            center: pickupLocation,
            zoom: 15,
            mapTypeId: google.maps.MapTypeId.roadmap,
            zoomControl: true,
            fullscreenControl: true,
            streetViewControl: false,
        });

        // Marker - Titik Jemput (Hijau)
        new google.maps.Marker({
            position: pickupLocation,
            map: map,
            label: { text: '📍', color: 'white', fontWeight: 'bold', fontSize: '14px' },
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                fillColor: '#22c55e',
                fillOpacity: 1,
                strokeColor: '#ffffff',
                strokeWeight: 2,
                scale: 16
            },
            title: 'Titik Jemput'
        });

        // Coba dapatkan lokasi driver saat ini
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const driverLocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                
                // Tambahkan marker lokasi driver
                new google.maps.Marker({
                    position: driverLocation,
                    map: map,
                    label: { text: 'Anda', color: 'white', fontSize: '12px' },
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        fillColor: '#3b82f6',
                        fillOpacity: 1,
                        strokeColor: '#ffffff',
                        strokeWeight: 2,
                        scale: 12
                    },
                    title: 'Lokasi Anda Saat Ini'
                });
                
                // Zoom ke area yang mencakup kedua titik
                const bounds = new google.maps.LatLngBounds();
                bounds.extend(pickupLocation);
                bounds.extend(driverLocation);
                map.fitBounds(bounds);
            });
        } else {
            // Jika geolocation tidak didukung, zoom ke titik jemput saja
            map.setZoom(15);
        }
        
        console.log('Peta siap, marker titik jemput ditampilkan');
    }
</script>

<style>
    .pac-container {
        background-color: #1f2937;
        border-color: #374151;
        border-radius: 0.5rem;
        z-index: 1000;
    }
    .pac-item {
        color: #e5e7eb;
        padding: 0.5rem 1rem;
        border-top-color: #374151;
        cursor: pointer;
    }
    .pac-item:hover {
        background-color: #374151;
    }
</style>
@endsection