@extends('layouts.driver')

@section('content')
<div class="max-w-6xl mx-auto px-4 mt-10">

    {{-- HEADER --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-white">
            🗺️ Rute Perjalanan
        </h2>
        <p class="text-sm text-gray-400 mt-1">
            Klik tombol di bawah untuk membuka navigasi di Google Maps
        </p>
    </div>

    {{-- MAP CARD --}}
    <div class="bg-gray-800/50 backdrop-blur rounded-2xl overflow-hidden border border-gray-700">

        {{-- GOOGLE MAPS (untuk preview lokasi) --}}
        <div id="map" class="w-full h-[400px]"></div>

        {{-- INFO PERJALANAN --}}
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 px-6 py-4 border-t border-gray-700">
            <div class="text-sm text-gray-300">
                <div> Titik Jemput: <span class="text-green-400">{{ Str::limit($order->pickup_location, 40) }}</span></div>
                <div> Tujuan: <span class="text-red-400">{{ Str::limit($order->destination, 40) }}</span></div>
                <div class="text-blue-400 text-xs mt-1">📱 Klik tombol hijau untuk navigasi</div>
            </div>

            <div class="flex gap-3">
                {{-- TOMBOL BUKA GOOGLE MAPS (NAVIGASI) --}}
                <a href="https://www.google.com/maps/dir/?api=1&origin={{ $order->pickup_lat }},{{ $order->pickup_lng }}&destination={{ $order->dest_lat }},{{ $order->dest_lng }}&travelmode=driving"
                   target="_blank"
                   class="px-6 py-2.5 rounded-xl bg-green-600 hover:bg-green-700 text-white font-semibold transition inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                    </svg>
                    🚀 Buka Google Maps (Navigasi)
                </a>

                {{-- TOMBOL SELESAIKAN PESANAN --}}
<a href="{{ route('driver.orders.finish', $order->id) }}" 
   class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold transition inline-block text-center">
     Selesaikan Pesanan
</a>

                <a href="{{ route('driver.order.detail', $order->id) }}"
                   class="px-6 py-2.5 rounded-xl border border-gray-600 text-gray-300 hover:bg-gray-700 transition">
                    ← Kembali
                </a>
            </div>
        </div>
    </div>

    {{-- INFORMASI TAMBAHAN --}}
    <div class="mt-4 bg-gray-800/30 rounded-xl p-3 text-center">
        <p class="text-xs text-gray-500">
            📍 Tombol navigasi akan membuka aplikasi Google Maps di HP atau web browser.
            Ikuti petunjuk arah dari Google Maps untuk mencapai tujuan.
        </p>
    </div>

</div>

{{-- SCRIPT UNTUK SUBMIT FORM --}}
<script>
    function submitCompleteForm() {
        console.log('Tombol Selesaikan Pesanan ditekan!');
        
        // Tampilkan loading atau pesan
        const btn = event.target;
        btn.innerHTML = '⏳ Memproses...';
        btn.disabled = true;
        
        // Submit form
        document.getElementById('completeForm').submit();
    }
</script>

{{-- GOOGLE MAPS SCRIPT --}}
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAjK4gQ_-XPPBdO4h_0Mz481pY6vpnMkAQ&libraries=places&callback=initMap" async defer></script>

<script>
    // Koordinat dari server
    const pickupLat = {{ $order->pickup_lat }};
    const pickupLng = {{ $order->pickup_lng }};
    const destLat = {{ $order->dest_lat }};
    const destLng = {{ $order->dest_lng }};

    let map;

    function initMap() {
        const pickupLocation = { lat: pickupLat, lng: pickupLng };
        const destinationLocation = { lat: destLat, lng: destLng };
        
        map = new google.maps.Map(document.getElementById('map'), {
            center: pickupLocation,
            zoom: 13,
            mapTypeId: google.maps.MapTypeId.roadmap,
            zoomControl: true,
            fullscreenControl: true,
            streetViewControl: false,
        });

        // Marker A - Titik Jemput (Hijau)
        new google.maps.Marker({
            position: pickupLocation,
            map: map,
            label: { text: 'A', color: 'white', fontWeight: 'bold', fontSize: '14px' },
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

        // Marker B - Tujuan (Merah)
        new google.maps.Marker({
            position: destinationLocation,
            map: map,
            label: { text: 'B', color: 'white', fontWeight: 'bold', fontSize: '14px' },
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                fillColor: '#ef4444',
                fillOpacity: 1,
                strokeColor: '#ffffff',
                strokeWeight: 2,
                scale: 16
            },
            title: 'Tujuan'
        });

        const bounds = new google.maps.LatLngBounds();
        bounds.extend(pickupLocation);
        bounds.extend(destinationLocation);
        map.fitBounds(bounds);
        
        console.log('Peta siap, marker A dan B ditampilkan');
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