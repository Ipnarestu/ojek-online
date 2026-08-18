<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'OMK OJOL - Driver Panel')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    {{-- 🔥 META TAGS UNTUK NOTIFIKASI --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth('driver')
    <meta name="driver-online" content="{{ auth('driver')->user()->online ?? false }}">
    <meta name="driver-work-status" content="{{ auth('driver')->user()->work_status ?? 'free' }}">
    <meta name="driver-id" content="{{ auth('driver')->user()->id ?? '' }}">
    @endauth

    <script src="https://cdn.tailwindcss.com"></script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"
          rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }
        
        /* Animasi loading spinner */
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .animate-spin-custom {
            animation: spin 1s linear infinite;
        }
        
        /* Badge online/offline */
        .status-badge {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 6px;
        }
        .status-online { background-color: #22c55e; box-shadow: 0 0 5px #22c55e; }
        .status-offline { background-color: #ef4444; }
        .status-busy { background-color: #eab308; }
        
        /* Active link styling */
        .nav-active {
            color: #22c55e;
            border-bottom: 2px solid #22c55e;
            padding-bottom: 4px;
        }
    </style>
</head>

<body class="min-h-screen
             bg-gradient-to-br from-slate-950 via-slate-900 to-slate-800
             text-gray-200">

{{-- ================= NAVBAR DRIVER ================= --}}
<header class="bg-black/40 backdrop-blur border-b border-white/10 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-6 py-4 flex flex-col md:flex-row justify-between items-center gap-3">

        <div class="flex items-center gap-3">
            <span class="text-lg font-bold text-green-400">
                 OMK OJOL | Driver
            </span>
            
            {{-- Status Driver --}}
            @auth('driver')
            <div class="flex items-center gap-2 ml-4">
                @if(auth('driver')->user()->online)
                    <span class="status-badge status-online"></span>
                    <span class="text-xs text-green-400">Online</span>
                @else
                    <span class="status-badge status-offline"></span>
                    <span class="text-xs text-red-400">Offline</span>
                @endif
                
                @if(auth('driver')->user()->work_status === 'busy')
                    <span class="status-badge status-busy"></span>
                    <span class="text-xs text-yellow-400">Sedang Bertugas</span>
                @endif
            </div>
            @endauth
        </div>

        <div class="flex items-center gap-4 text-sm flex-wrap justify-center">
            <a href="{{ route('driver.dashboard') }}" 
               class="hover:text-white transition {{ request()->routeIs('driver.dashboard') ? 'text-green-400' : '' }}">
                 Dashboard
            </a>

            <a href="{{ route('driver.orders') }}" 
               class="hover:text-white transition {{ request()->routeIs('driver.orders*') ? 'text-green-400' : '' }}">
                 Pesanan
            </a>

            {{--  PERBAIKAN: Ganti driver.order.history menjadi driver.orders.history --}}
            <a href="{{ route('driver.orders.history') }}" 
               class="hover:text-white transition {{ request()->routeIs('driver.orders.history') ? 'text-green-400' : '' }}">
                 Riwayat
            </a>

            <a href="{{ route('driver.wallet') }}" 
               class="hover:text-white transition {{ request()->routeIs('driver.wallet*') ? 'text-green-400' : '' }}">
                 Wallet
            </a>

            <a href="{{ route('driver.earnings') }}" 
               class="hover:text-white transition {{ request()->routeIs('driver.earnings*') ? 'text-green-400' : '' }}">
                 Pendapatan
            </a>

            <a href="{{ route('driver.kendaraan.index') }}" 
               class="hover:text-white transition {{ request()->routeIs('driver.kendaraan*') ? 'text-green-400' : '' }}">
                 Kendaraan
            </a>

            <form method="POST" action="{{ route('driver.logout') }}" class="inline">
                @csrf
                <button type="submit" class="text-red-400 hover:text-red-300 transition">
                     Logout
                </button>
            </form>
        </div>
    </div>
</header>

{{-- ================= MAIN ================= --}}
<main class="relative z-10 pb-10">
    @yield('content')
</main>

{{-- Include modal notifikasi --}}
@include('driver.partials.order-notification')

<script>
    // 🔥 Update meta tags secara dinamis jika status driver berubah
    function updateDriverStatus(online, workStatus) {
        const onlineMeta = document.querySelector('meta[name="driver-online"]');
        const workStatusMeta = document.querySelector('meta[name="driver-work-status"]');
        
        if (onlineMeta) onlineMeta.content = online;
        if (workStatusMeta) workStatusMeta.content = workStatus;
        
        // Update tampilan status di navbar
        setTimeout(() => {
            location.reload();
        }, 500);
    }
    
    // 🔥 Cek status driver secara berkala (opsional)
    function checkDriverStatus() {
        fetch('/driver/order/status', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const onlineMeta = document.querySelector('meta[name="driver-online"]');
                const workStatusMeta = document.querySelector('meta[name="driver-work-status"]');
                
                if (onlineMeta) onlineMeta.content = data.online;
                if (workStatusMeta) workStatusMeta.content = data.work_status;
            }
        })
        .catch(err => console.error('Error checking status:', err));
    }
    
    // 🔥 Toast notification helper
    function showToast(message, type = 'success') {
        let toast = document.createElement('div');
        let bgColor = type === 'success' ? 'bg-green-500' : (type === 'error' ? 'bg-red-500' : 'bg-blue-500');
        toast.className = `fixed bottom-4 right-4 ${bgColor} text-white px-4 py-2 rounded-lg shadow-lg z-50 transition-all duration-300`;
        toast.innerText = message;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
    
    // 🔥 Konfirmasi logout
    document.addEventListener('DOMContentLoaded', function() {
        const logoutForm = document.querySelector('form[action="{{ route('driver.logout') }}"]');
        if (logoutForm) {
            logoutForm.addEventListener('submit', function(e) {
                if (!confirm('Apakah Anda yakin ingin logout?')) {
                    e.preventDefault();
                }
            });
        }
    });
    
    // Cek status setiap 30 detik (opsional, aktifkan jika perlu)
    // setInterval(checkDriverStatus, 30000);
</script>

</body>
</html>