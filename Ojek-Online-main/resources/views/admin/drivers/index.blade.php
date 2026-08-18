@extends('layouts.admin')

@section('content')
<div class="space-y-6">

    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-semibold text-white">Driver Management</h2>
            <p class="text-sm text-gray-400 mt-1">Kelola verifikasi driver yang mendaftar</p>
        </div>
    </div>

    {{-- STATUS FILTER --}}
    <div class="flex gap-2 flex-wrap">
        <a href="{{ route('admin.drivers.index') }}?status=all" 
           class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-700 text-gray-300">Semua</a>
        <a href="{{ route('admin.drivers.index') }}?status=pending" 
           class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-500/20 text-yellow-400">Pending</a>
        <a href="{{ route('admin.drivers.index') }}?status=approved" 
           class="px-3 py-1 rounded-full text-xs font-semibold bg-green-500/20 text-green-400">Approved</a>
        <a href="{{ route('admin.drivers.index') }}?status=rejected" 
           class="px-3 py-1 rounded-full text-xs font-semibold bg-red-500/20 text-red-400">Rejected</a>
    </div>

    {{-- DAFTAR DRIVER --}}
    @forelse($drivers as $driver)
    <div class="bg-slate-900/70 backdrop-blur border border-white/10 rounded-2xl p-6 flex flex-col md:flex-row md:items-start md:justify-between gap-6">

        <div class="space-y-2 flex-1">
            <p class="text-lg font-semibold text-white">{{ $driver->name }}</p>
            <p class="text-sm text-gray-400">{{ $driver->email }} | {{ $driver->phone }}</p>
            
            {{-- VERIFICATION STATUS --}}
            <div class="text-sm flex items-center gap-2">
                <span class="text-gray-400">Verifikasi:</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                    @if($driver->verification_status == 'approved') bg-green-500/20 text-green-400
                    @elseif($driver->verification_status == 'rejected') bg-red-500/20 text-red-400
                    @else bg-yellow-500/20 text-yellow-400
                    @endif">
                    {{ ucfirst($driver->verification_status ?? 'Pending') }}
                </span>
            </div>

            {{-- ONLINE STATUS --}}
            <div class="text-sm flex items-center gap-2">
                <span class="text-gray-400">Online:</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                    {{ $driver->online ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-400' }}">
                    {{ $driver->online ? 'YA' : 'TIDAK' }}
                </span>
            </div>

            {{-- DOKUMEN --}}
            @if($driver->ktp_path || $driver->sim_path || $driver->stnk_path || $driver->photo_path)
            <div class="pt-2">
                <p class="text-gray-400 text-sm mb-1">Dokumen:</p>
                <div class="flex gap-2 flex-wrap">
                    @if($driver->ktp_path)
                    <a href="{{ route('admin.drivers.download', [$driver->id, 'ktp_path']) }}" 
                       class="text-xs bg-blue-600/20 text-blue-400 px-2 py-1 rounded hover:bg-blue-600/40">📄 KTP</a>
                    @endif
                    @if($driver->sim_path)
                    <a href="{{ route('admin.drivers.download', [$driver->id, 'sim_path']) }}" 
                       class="text-xs bg-blue-600/20 text-blue-400 px-2 py-1 rounded hover:bg-blue-600/40">📄 SIM</a>
                    @endif
                    @if($driver->stnk_path)
                    <a href="{{ route('admin.drivers.download', [$driver->id, 'stnk_path']) }}" 
                       class="text-xs bg-blue-600/20 text-blue-400 px-2 py-1 rounded hover:bg-blue-600/40">📄 STNK</a>
                    @endif
                    @if($driver->photo_path)
                    <a href="{{ route('admin.drivers.download', [$driver->id, 'photo_path']) }}" 
                       class="text-xs bg-blue-600/20 text-blue-400 px-2 py-1 rounded hover:bg-blue-600/40">📸 Foto</a>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <div class="flex flex-col gap-3 text-sm min-w-[180px]">
            @if($driver->verification_status == 'pending')
                <form action="{{ route('admin.drivers.verify', $driver->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 rounded-xl bg-green-600 hover:bg-green-700 text-white font-semibold transition">
                        ✅ Verifikasi
                    </button>
                </form>
                <button onclick="showRejectModal({{ $driver->id }})" 
                        class="w-full px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-semibold transition">
                    ❌ Tolak
                </button>
            @endif

            <form method="POST" action="{{ route('admin.drivers.toggle', $driver->id) }}">
                @csrf
                <button class="w-full px-4 py-2 rounded-xl font-semibold transition
                    {{ $driver->online ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white">
                    {{ $driver->online ? 'Nonaktifkan' : 'Aktifkan' }}
                </button>
            </form>

            <a href="{{ route('admin.drivers.orders', $driver->id) }}"
               class="w-full text-center px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold transition">
                Lihat Pesanan
            </a>
        </div>
    </div>
    @empty
    <div class="bg-slate-900/70 backdrop-blur border border-white/10 rounded-2xl p-12 text-center">
        <p class="text-gray-400">Belum ada driver yang mendaftar</p>
        <p class="text-gray-500 text-sm mt-2">Driver yang mendaftar akan muncul di sini</p>
    </div>
    @endforelse

    {{-- PAGINATION --}}
    <div class="mt-4">
        {{ $drivers->links() }}
    </div>
</div>

{{-- MODAL TOLAK --}}
<div id="rejectModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 backdrop-blur-sm">
    <div class="w-full max-w-md bg-gray-900 rounded-2xl p-6">
        <h3 class="text-xl font-bold text-white mb-4">Tolak Pendaftaran Driver</h3>
        <form id="rejectForm" method="POST">
            @csrf
            <textarea name="rejection_reason" rows="4" 
                      class="w-full rounded-lg bg-gray-800 border border-gray-700 text-white px-4 py-2" 
                      placeholder="Berikan alasan penolakan..." required></textarea>
            <div class="flex gap-3 mt-4">
                <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg">Kirim</button>
                <button type="button" onclick="closeRejectModal()" class="flex-1 bg-gray-700 hover:bg-gray-600 text-white py-2 rounded-lg">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
    function showRejectModal(driverId) {
        document.getElementById('rejectForm').action = '/admin/drivers/' + driverId + '/reject';
        document.getElementById('rejectModal').classList.remove('hidden');
        document.getElementById('rejectModal').classList.add('flex');
    }
    
    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
        document.getElementById('rejectModal').classList.remove('flex');
    }
</script>
@endsection