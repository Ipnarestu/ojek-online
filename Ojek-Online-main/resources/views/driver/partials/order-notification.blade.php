{{-- MODAL NOTIFIKASI PESANAN BARU --}}
<div id="notificationModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 backdrop-blur-sm">
    <div class="w-full max-w-sm mx-4 bg-white rounded-2xl shadow-2xl overflow-hidden animate-bounce-in">
        
        {{-- HEADER --}}
        <div class="bg-green-500 px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <span class="text-white font-semibold">Otomatis menerima Pesanan Baru</span>
            </div>
            <button onclick="closeNotificationModal()" class="text-white/70 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        {{-- BODY --}}
        <div class="p-4">
            {{-- Tipe Layanan --}}
            <div class="flex items-center gap-2 mb-3">
                <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-800">Antar Jemput</p>
                    <p class="text-xs text-gray-500">500 g, 0x0x0 cm</p>
                </div>
            </div>

            {{-- Detail Pesanan --}}
            <div class="bg-gray-50 rounded-xl p-3 mb-3">
                <div class="mb-2">
                    <p class="text-xs text-gray-500"> Titik Jemput</p>
                    <p class="text-sm font-medium text-gray-800" id="notifPickup">-</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500"> Tujuan</p>
                    <p class="text-sm font-medium text-gray-800" id="notifDestination">-</p>
                </div>
            </div>

            {{-- Pendapatan --}}
            <div class="flex justify-between items-center mb-4">
                <span class="text-gray-600">Pendapatan</span>
                <span class="text-xl font-bold text-green-600" id="notifPrice">Rp 0</span>
            </div>

            {{-- Tombol --}}
            <div class="flex gap-3">
                <button onclick="acceptAndClose()" class="flex-1 bg-green-500 hover:bg-green-600 text-white font-semibold py-2.5 rounded-xl transition">
                    OK
                </button>
                <button onclick="acceptAndViewDetail()" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2.5 rounded-xl transition">
                    Detail Pesanan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let pendingOrderId = null;
    let pendingOrderData = null;
    let pollingInterval = null;
    let isProcessing = false; // Mencegah multiple accept

    // Suara notifikasi
    function playNotificationSound() {
        try {
            let audio = new Audio('https://www.soundjay.com/misc/sounds/bell-ringing-05.mp3');
            audio.volume = 0.5;
            audio.play().catch(e => console.log('Audio error:', e));
        } catch(e) { console.log('Audio not supported'); }
    }

    // Fungsi untuk menerima pesanan
    function acceptOrder(orderId, redirectToDetail = false) {
        if (isProcessing) return;
        isProcessing = true;

        fetch('/driver/orders/' + orderId + '/accept', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (redirectToDetail) {
                    window.location.href = '/driver/orders/' + orderId + '/detail';
                } else {
                    closeNotificationModal();
                    // Optional: Tampilkan toast sukses
                    showToast('Pesanan berhasil diterima!');
                }
            } else {
                alert('Gagal menerima pesanan: ' + data.message);
                closeNotificationModal();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menerima pesanan');
            closeNotificationModal();
        })
        .finally(() => {
            isProcessing = false;
        });
    }

    // OK - Terima pesanan dan tutup modal
    function acceptAndClose() {
        if (pendingOrderId) {
            acceptOrder(pendingOrderId, false);
        }
    }

    // Detail Pesanan - Terima pesanan dan langsung ke halaman detail
    function acceptAndViewDetail() {
        if (pendingOrderId) {
            acceptOrder(pendingOrderId, true);
        }
    }

    function showModal(order) {
        if (isProcessing) return;
        
        pendingOrderId = order.id;
        pendingOrderData = order;
        
        document.getElementById('notifPickup').innerText = order.pickup_location;
        document.getElementById('notifDestination').innerText = order.destination;
        document.getElementById('notifPrice').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(order.price);
        
        document.getElementById('notificationModal').classList.remove('hidden');
        document.getElementById('notificationModal').classList.add('flex');
        
        playNotificationSound();
    }

    function closeNotificationModal() {
        document.getElementById('notificationModal').classList.add('hidden');
        document.getElementById('notificationModal').classList.remove('flex');
    }

    function checkNewOrders() {
        // Jangan cek jika sedang memproses atau modal terbuka
        if (isProcessing) return;

        console.log('Mengecek pesanan baru...');
        
        fetch('{{ route("driver.check.new.orders") }}')
            .then(res => res.json())
            .then(data => {
                if (data.has_new_order && data.order && !pendingOrderId) {
                    showModal(data.order);
                }
            })
            .catch(err => console.error('Fetch error:', err));
    }

    // Toast notifikasi kecil (opsional)
    function showToast(message) {
        let toast = document.createElement('div');
        toast.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
        toast.innerText = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }

    // Mulai polling
    if (pollingInterval === null) {
        pollingInterval = setInterval(checkNewOrders, 5000);
    }
    setTimeout(checkNewOrders, 2000);
</script>

<style>
    @keyframes bounce-in {
        0% { transform: scale(0.8); opacity: 0; }
        60% { transform: scale(1.05); }
        100% { transform: scale(1); opacity: 1; }
    }
    .animate-bounce-in {
        animation: bounce-in 0.3s ease-out;
    }
</style>