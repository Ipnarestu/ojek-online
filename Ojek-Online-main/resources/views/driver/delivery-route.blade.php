@extends('layouts.driver')

@section('content')
<div class="max-w-6xl mx-auto px-4 mt-10">

    {{-- HEADER --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-white">
            🗺️ Rute Menuju Tujuan
        </h2>
        <p class="text-sm text-gray-400 mt-1">
            Klik tombol di bawah untuk membuka navigasi ke lokasi tujuan
        </p>
    </div>

    {{-- MAP CARD --}}
    <div class="bg-gray-800/50 backdrop-blur rounded-2xl overflow-hidden border border-gray-700">
        <div id="map" class="w-full h-[400px]"></div>
    </div>

    {{-- INFO PERJALANAN (Tujuan di atas, Jemput di bawah) --}}
    <div class="mt-4 bg-gray-800/50 rounded-xl p-4">
        {{-- LOKASI TUJUAN (BESAR) --}}
        <div class="mb-4 p-3 bg-red-900/30 rounded-lg border border-red-700/30">
            <p class="text-xs text-red-400 uppercase tracking-wide">🎯 Tujuan</p>
            <p class="text-white font-semibold text-lg">{{ $order->destination }}</p>
        </div>

        {{-- LOKASI JEMPUT (KECIL/GELAP) --}}
        <div class="p-3 bg-gray-800/50 rounded-lg">
            <p class="text-xs text-gray-500 uppercase tracking-wide">📍 Titik Jemput</p>
            <p class="text-gray-400 text-sm">{{ $order->pickup_location }}</p>
        </div>
    </div>

    {{-- TOMBOL --}}
    <div class="mt-6 flex gap-3">
        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $order->dest_lat }},{{ $order->dest_lng }}&travelmode=driving"
           target="_blank"
           class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl transition text-center">
            🗺️ Navigasi ke Tujuan
        </a>
        <a href="{{ route('driver.order.detail', $order->id) }}"
           class="px-6 py-3 rounded-xl border border-gray-600 text-gray-300 hover:bg-gray-700 transition">
            ← Kembali
        </a>
    </div>

</div>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAjK4gQ_-XPPBdO4h_0Mz481pY6vpnMkAQ&libraries=places&callback=initMap" async defer></script>
<script>
    const destLat = {{ $order->dest_lat }};
    const destLng = {{ $order->dest_lng }};
    let map;

    function initMap() {
        const destinationLocation = { lat: destLat, lng: destLng };
        map = new google.maps.Map(document.getElementById('map'), {
            center: destinationLocation,
            zoom: 15,
            mapTypeId: google.maps.MapTypeId.roadmap,
        });
        new google.maps.Marker({
            position: destinationLocation,
            map: map,
            label: { text: '🎯', color: 'white', fontWeight: 'bold' },
            icon: { path: google.maps.SymbolPath.CIRCLE, fillColor: '#ef4444', fillOpacity: 1, strokeColor: '#ffffff', strokeWeight: 2, scale: 16 },
            title: 'Tujuan'
        });
    }
</script>
@endsection