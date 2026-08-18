@extends('layouts.main')

@section('content')
<div class="max-w-6xl mx-auto mt-10 px-4">
    <div class="rounded-2xl bg-gray-900/80 backdrop-blur border border-gray-800 shadow-xl p-8">

        {{-- HEADER --}}
        <div class="mb-6 text-center">
            <h2 class="text-2xl font-semibold text-white">Buat Pesanan</h2>
            <p class="text-sm text-gray-400 mt-1">
                @if($type === 'ride') 
                    Antar Jemput
                @elseif($type === 'delivery') 
                    Kirim Barang
                @else 
                    Pesan & Belanja
                @endif
            </p>
            <p class="text-xs text-green-500 mt-2">📍 Melayani area Universitas Indonesia, Depok</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- FORM KIRI --}}
            <div>
                <form action="{{ route('user.order.submit') }}" method="POST" class="space-y-5" id="orderForm">
                    @csrf
                    <input type="hidden" name="type" value="{{ $type }}">
                    
                    {{-- PICKUP (hidden, diisi JS) --}}
                    <input type="hidden" name="pickup" id="pickupAddress">
                    <input type="hidden" name="pickup_lat" id="pickupLat">
                    <input type="hidden" name="pickup_lng" id="pickupLng">
                    
                    {{-- DESTINATION (hidden, diisi JS) --}}
                    <input type="hidden" name="destination" id="destAddress">
                    <input type="hidden" name="dest_lat" id="destLat">
                    <input type="hidden" name="dest_lng" id="destLng">
                    <input type="hidden" name="price" id="priceHidden">
                    
                    {{-- TITIK JEMPUT --}}
                    <div class="relative">
                        <label class="block text-sm text-gray-300 mb-1">Titik Jemput</label>
                        <input type="text"
                               id="pickupInput"
                               placeholder="Cari lokasi jemputan... (contoh: Fakultas, Gedung, Stasiun)"
                               class="w-full rounded-lg bg-gray-800 border border-gray-700 text-white placeholder-gray-500 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-600"
                               autocomplete="off">
                        <div id="pickupSuggestions" class="absolute z-50 w-full bg-gray-800 border border-gray-700 rounded-lg mt-1 hidden max-h-60 overflow-y-auto"></div>
                    </div>
                    <div id="pickupDisplay" class="text-green-400 text-xs mt-1 hidden">
                        ✓ Lokasi dipilih: <span id="pickupDisplayText"></span>
                    </div>
                    
                    {{-- TUJUAN --}}
                    <div class="relative mt-4">
                        <label class="block text-sm text-gray-300 mb-1">Tujuan</label>
                        <input type="text"
                               id="destinationInput"
                               placeholder="Cari tujuan... (contoh: Fakultas, Gedung, Stasiun)"
                               class="w-full rounded-lg bg-gray-800 border border-gray-700 text-white placeholder-gray-500 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-600"
                               autocomplete="off">
                        <div id="destSuggestions" class="absolute z-50 w-full bg-gray-800 border border-gray-700 rounded-lg mt-1 hidden max-h-60 overflow-y-auto"></div>
                    </div>
                    <div id="destDisplay" class="text-green-400 text-xs mt-1 hidden">
                        ✓ Lokasi dipilih: <span id="destDisplayText"></span>
                    </div>
                    
                    {{-- METODE PEMBAYARAN --}}
                    <div class="space-y-3">
                        <label class="block text-sm text-gray-300 mb-1">Metode Pembayaran</label>
                        
                        <div class="grid grid-cols-3 gap-3">
                            {{-- COD --}}
                            <label class="relative flex cursor-pointer">
                                <input type="radio" name="payment_method" value="cod" class="peer sr-only" checked>
                                <div class="w-full bg-gray-800 border border-gray-700 rounded-xl p-3 text-center transition-all peer-checked:border-green-500 peer-checked:bg-green-900/30 cursor-pointer">
                                    <div class="text-2xl mb-1">💵</div>
                                    <div class="text-sm font-medium text-white">Cash</div>
                                    <div class="text-xs text-gray-400">Bayar langsung</div>
                                </div>
                            </label>
                            
                            {{-- QRIS --}}
                            <label class="relative flex cursor-pointer">
                                <input type="radio" name="payment_method" value="qris" class="peer sr-only" id="qrisRadio">
                                <div class="w-full bg-gray-800 border border-gray-700 rounded-xl p-3 text-center transition-all peer-checked:border-green-500 peer-checked:bg-green-900/30 cursor-pointer">
                                    <div class="text-2xl mb-1">📱</div>
                                    <div class="text-sm font-medium text-white">QRIS</div>
                                    <div class="text-xs text-gray-400">Scan QR Code</div>
                                </div>
                            </label>
                            
                            {{-- SALDO WALLET --}}
                            <label class="relative flex cursor-pointer">
                                <input type="radio" name="payment_method" value="wallet" class="peer sr-only">
                                <div class="w-full bg-gray-800 border border-gray-700 rounded-xl p-3 text-center transition-all peer-checked:border-green-500 peer-checked:bg-green-900/30 cursor-pointer">
                                    <div class="text-2xl mb-1">👛</div>
                                    <div class="text-sm font-medium text-white">Saldo</div>
                                    <div class="text-xs text-gray-400">Rp {{ number_format(Auth::user()->wallet->balance ?? 0, 0, ',', '.') }}</div>
                                </div>
                            </label>
                        </div>
                    </div>
                    
                    {{-- CATATAN --}}
                    <div>
                        <label class="block text-sm text-gray-300 mb-1">Catatan (Opsional)</label>
                        <textarea name="note" rows="3" placeholder="Contoh: Jemput di gerbang samping" class="w-full rounded-lg bg-gray-800 border border-gray-700 text-white placeholder-gray-500 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-600"></textarea>
                    </div>
                    
                    {{-- ESTIMASI HARGA --}}
                    <div class="bg-green-900/30 rounded-xl p-4 border border-green-700/50">
                        <label class="block text-sm text-gray-300 mb-1">Estimasi Harga</label>
                        <div class="text-3xl font-bold text-green-400" id="priceEst">
                            Rp 15.000
                        </div>
                        <p class="text-xs text-gray-500 mt-2">*Harga tetap Rp 15.000 untuk area UI</p>
                    </div>
                    
                    <button type="submit" id="submitBtn" class="w-full mt-2 bg-green-600 hover:bg-green-700 text-white font-medium py-3 rounded-xl transition">
                        Cari Driver Terdekat
                    </button>
                </form>
            </div>
            
            {{-- MAP KANAN --}}
            <div>
                <div class="bg-gray-800 rounded-xl overflow-hidden">
                    <div id="map" style="height: 500px; width: 100%;"></div>
                    <div class="p-3 bg-gray-800/50 text-center">
                        <span class="text-sm text-gray-400">📍 Klik pada rekomendasi lokasi untuk memilih</span>
                    </div>
                </div>
            </div>
        </div>
        
        <a href="{{ route('user.home') }}" class="block text-center text-sm text-gray-400 hover:text-white mt-6">
            ← Kembali
        </a>
    </div>
</div>

{{-- MODAL QR CODE --}}
<div id="qrisModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 backdrop-blur-sm">
    <div class="w-full max-w-sm mx-4 bg-white rounded-2xl shadow-2xl overflow-hidden animate-bounce-in">
        
        <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4 text-center">
            <h3 class="text-xl font-bold text-white">Scan QR Code</h3>
            <p class="text-green-100 text-sm">Lakukan pembayaran melalui QRIS</p>
        </div>
        
        <div class="p-6 text-center">
            {{-- GAMBAR QR CODE --}}
            <img src="{{ asset('images/qris-code.png') }}" 
                 alt="QRIS Code" 
                 class="w-64 h-64 mx-auto mb-4 border border-gray-200 rounded-xl">
            
            <p class="text-gray-600 text-sm mb-2">Nominal: <strong id="modalPrice">Rp 15.000</strong></p>
            <p class="text-gray-500 text-xs">Scan menggunakan aplikasi m-banking atau e-wallet</p>
            
            <div class="flex gap-3 mt-6">
                <button onclick="closeQrisModal()" 
                        class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 rounded-xl transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

{{-- GOOGLE MAPS SCRIPT --}}
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAjK4gQ_-XPPBdO4h_0Mz481pY6vpnMkAQ&callback=initMap" async defer></script>

<script>
    // ========================================
    // DATABASE LOKASI UI
    // ========================================
    const locationsDB = [
        { name: "Fakultas Hukum UI", lat: -6.3610, lng: 106.7870 },
        { name: "Fakultas Kedokteran UI", lat: -6.3585, lng: 106.7895 },
        { name: "Fakultas Ekonomi dan Bisnis UI", lat: -6.3630, lng: 106.7900 },
        { name: "Fakultas Teknik UI", lat: -6.3650, lng: 106.7930 },
        { name: "Fakultas Ilmu Komputer UI", lat: -6.3665, lng: 106.7950 },
        { name: "Fakultas Psikologi UI", lat: -6.3605, lng: 106.7910 },
        { name: "Fakultas Ilmu Sosial dan Ilmu Politik UI", lat: -6.3620, lng: 106.7885 },
        { name: "Fakultas Matematika dan Ilmu Pengetahuan Alam UI", lat: -6.3640, lng: 106.7940 },
        { name: "Fakultas Ilmu Budaya UI", lat: -6.3595, lng: 106.7875 },
        { name: "Fakultas Kesehatan Masyarakat UI", lat: -6.3570, lng: 106.7905 },
        { name: "Fakultas Farmasi UI", lat: -6.3590, lng: 106.7925 },
        { name: "Fakultas Ilmu Keperawatan UI", lat: -6.3575, lng: 106.7915 },
        { name: "Program Pendidikan Vokasi UI", lat: -6.3670, lng: 106.7960 },
        { name: "Rektorat UI", lat: -6.3628, lng: 106.7920 },
        { name: "Perpustakaan Pusat UI", lat: -6.3635, lng: 106.7935 },
        { name: "Balairung UI", lat: -6.3615, lng: 106.7895 },
        { name: "Pusat Administrasi UI", lat: -6.3645, lng: 106.7915 },
        { name: "Asrama Mahasiswa UI", lat: -6.3700, lng: 106.8000 },
        { name: "Gerbang Utama UI (Tugu Buku)", lat: -6.3605, lng: 106.7885 },
        { name: "Pintu Pondok Cina", lat: -6.3550, lng: 106.7820 },
        { name: "Kukusan Kelurahan (Kukel)", lat: -6.3650, lng: 106.7950 },
        { name: "Kukusan Teknik (Kutek)", lat: -6.3680, lng: 106.7980 },
        { name: "PNJ (Politeknik Negeri Jakarta)", lat: -6.3750, lng: 106.8050 },
        { name: "Stasiun UI", lat: -6.3580, lng: 106.7920 }
    ];

    // ========================================
    // VARIABLES
    // ========================================
    var map;
    var pickupMarker = null;
    var destMarker = null;
    var pickupLocation = null;
    var destLocation = null;

    const UI_CENTER = { lat: -6.3628, lng: 106.7920 };
    const UI_ZOOM = 15;

    // ========================================
    // INIT MAP
    // ========================================
    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            center: UI_CENTER,
            zoom: UI_ZOOM,
            mapTypeId: google.maps.MapTypeId.roadmap,
            zoomControl: true,
            fullscreenControl: true,
            streetViewControl: false,
        });

        setupInputListeners();
    }

    function searchLocations(keyword, maxResults = 10) {
        if (!keyword || keyword.length < 2) return [];
        const lowerKeyword = keyword.toLowerCase();
        const results = [];
        for (const loc of locationsDB) {
            if (loc.name.toLowerCase().includes(lowerKeyword)) {
                results.push(loc);
            }
            if (results.length >= maxResults) break;
        }
        return results;
    }

    function showSuggestions(inputElement, suggestionsDiv, results, targetType) {
        if (results.length === 0) {
            suggestionsDiv.classList.add('hidden');
            return;
        }
        
        suggestionsDiv.innerHTML = '';
        for (const loc of results) {
            const item = document.createElement('div');
            item.className = 'px-4 py-2 hover:bg-gray-700 cursor-pointer text-white text-sm border-b border-gray-700 last:border-0';
            item.innerHTML = ` ${loc.name}`;
            item.onclick = () => {
                if (targetType === 'pickup') {
                    setPickupLocation(loc.lat, loc.lng, loc.name);
                    document.getElementById('pickupInput').value = loc.name;
                } else {
                    setDestinationLocation(loc.lat, loc.lng, loc.name);
                    document.getElementById('destinationInput').value = loc.name;
                }
                suggestionsDiv.classList.add('hidden');
                map.setCenter({ lat: loc.lat, lng: loc.lng });
                map.setZoom(17);
            };
            suggestionsDiv.appendChild(item);
        }
        suggestionsDiv.classList.remove('hidden');
    }

    function setupInputListeners() {
        const pickupInput = document.getElementById('pickupInput');
        const destInput = document.getElementById('destinationInput');
        const pickupSugg = document.getElementById('pickupSuggestions');
        const destSugg = document.getElementById('destSuggestions');
        
        let pickupTimeout, destTimeout;
        
        pickupInput.addEventListener('input', function() {
            clearTimeout(pickupTimeout);
            pickupTimeout = setTimeout(() => {
                const results = searchLocations(this.value);
                showSuggestions(pickupInput, pickupSugg, results, 'pickup');
            }, 300);
        });
        
        destInput.addEventListener('input', function() {
            clearTimeout(destTimeout);
            destTimeout = setTimeout(() => {
                const results = searchLocations(this.value);
                showSuggestions(destInput, destSugg, results, 'destination');
            }, 300);
        });
        
        document.addEventListener('click', function(e) {
            if (!pickupInput.contains(e.target) && !pickupSugg.contains(e.target)) {
                pickupSugg.classList.add('hidden');
            }
            if (!destInput.contains(e.target) && !destSugg.contains(e.target)) {
                destSugg.classList.add('hidden');
            }
        });
    }

    function setPickupLocation(lat, lng, address) {
        pickupLocation = { lat, lng, address };
        
        document.getElementById('pickupDisplay').classList.remove('hidden');
        document.getElementById('pickupDisplayText').innerHTML = address;
        document.getElementById('pickupAddress').value = address;
        document.getElementById('pickupLat').value = lat;
        document.getElementById('pickupLng').value = lng;
        document.getElementById('pickupInput').value = address;
        
        if (pickupMarker) pickupMarker.setMap(null);
        pickupMarker = new google.maps.Marker({
            position: { lat, lng },
            map: map,
            label: { text: 'A', color: 'white', fontWeight: 'bold' },
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                fillColor: '#22c55e',
                fillOpacity: 1,
                strokeColor: '#ffffff',
                strokeWeight: 2,
                scale: 16
            }
        });
        
        if (destLocation) checkAndEnableSubmit();
    }

    function setDestinationLocation(lat, lng, address) {
        destLocation = { lat, lng, address };
        
        document.getElementById('destDisplay').classList.remove('hidden');
        document.getElementById('destDisplayText').innerHTML = address;
        document.getElementById('destAddress').value = address;
        document.getElementById('destLat').value = lat;
        document.getElementById('destLng').value = lng;
        document.getElementById('destinationInput').value = address;
        
        if (destMarker) destMarker.setMap(null);
        destMarker = new google.maps.Marker({
            position: { lat, lng },
            map: map,
            label: { text: 'B', color: 'white', fontWeight: 'bold' },
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                fillColor: '#ef4444',
                fillOpacity: 1,
                strokeColor: '#ffffff',
                strokeWeight: 2,
                scale: 16
            }
        });
        
        if (pickupLocation) checkAndEnableSubmit();
    }

    function checkAndEnableSubmit() {
        if (pickupLocation && destLocation) {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = false;
            submitBtn.classList.remove('bg-gray-600', 'cursor-not-allowed');
            submitBtn.classList.add('bg-green-700', 'hover:bg-green-800', 'cursor-pointer');
        }
    }

    // Form submit validation
    document.getElementById('orderForm').addEventListener('submit', function(e) {
        if (!pickupLocation || !destLocation) {
            e.preventDefault();
            alert(' Silakan pilih titik jemput dan tujuan terlebih dahulu');
            return false;
        }
        
        let price = document.getElementById('priceHidden').value;
        if (!price || price <= 0) {
            const defaultPrice = 15000;
            document.getElementById('priceHidden').value = defaultPrice;
            document.getElementById('priceEst').innerHTML = 'Rp ' + defaultPrice.toLocaleString('id-ID');
        }
        
        return true;
    });

    // ========================================
    // QRIS MODAL FUNCTIONS
    // ========================================
    const qrisRadio = document.getElementById('qrisRadio');
    const qrisModal = document.getElementById('qrisModal');
    const modalPrice = document.getElementById('modalPrice');

    qrisRadio.addEventListener('change', function() {
        if (this.checked) {
            // Tampilkan modal QRIS
            qrisModal.classList.remove('hidden');
            qrisModal.classList.add('flex');
            
            // Update nominal di modal
            const price = document.getElementById('priceEst').innerText;
            modalPrice.innerText = price;
        }
    });

    function closeQrisModal() {
        qrisModal.classList.add('hidden');
        qrisModal.classList.remove('flex');
    }

    // Tutup modal jika klik di luar area modal
    qrisModal.addEventListener('click', function(e) {
        if (e.target === qrisModal) {
            closeQrisModal();
        }
    });
</script>

<style>
    #pickupSuggestions, #destSuggestions {
        background-color: #1f2937;
        border-color: #374151;
        border-radius: 0.5rem;
        margin-top: 0.25rem;
        z-index: 1000;
    }
    #pickupSuggestions div, #destSuggestions div {
        color: #e5e7eb;
        padding: 0.5rem 1rem;
        border-top-color: #374151;
        cursor: pointer;
    }
    #pickupSuggestions div:hover, #destSuggestions div:hover {
        background-color: #374151;
    }
    
    @keyframes bounce-in {
        0% { transform: scale(0.9); opacity: 0; }
        60% { transform: scale(1.05); }
        100% { transform: scale(1); opacity: 1; }
    }
    .animate-bounce-in {
        animation: bounce-in 0.3s ease-out;
    }
</style>
@endsection