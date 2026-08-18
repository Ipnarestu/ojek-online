@extends('layouts.main')

@section('content')
<div class="max-w-lg mx-auto mt-10 px-4">
    <div class="rounded-2xl bg-gray-900/80 backdrop-blur border border-gray-800 shadow-xl p-8">
        
        <h2 class="text-2xl font-bold text-white text-center mb-6">⭐ Beri Rating & Tip</h2>
        
        <div class="bg-gray-800/50 rounded-xl p-4 mb-6">
            <p class="text-gray-400">Pesanan #{{ $pesanan->id }}</p>
            <p class="text-white">Driver: {{ $pesanan->driver->name ?? '-' }}</p>
            <p class="text-green-400">Harga: Rp {{ number_format($pesanan->price, 0, ',', '.') }}</p>
        </div>
        
        <form action="{{ route('user.order.rate', $pesanan->id) }}" method="POST">
            @csrf
            
            {{-- RATING BINTANG --}}
            <div class="mb-6">
                <label class="block text-sm text-gray-300 mb-2">Rating (1-5 bintang)</label>
                <div class="flex gap-2 text-4xl" id="starRating">
                    @for($i = 1; $i <= 5; $i++)
                    <span class="cursor-pointer text-gray-500 hover:text-yellow-400 transition" data-value="{{ $i }}">☆</span>
                    @endfor
                </div>
                <input type="hidden" name="rating" id="ratingValue" required>
            </div>
            
            {{-- REVIEW --}}
            <div class="mb-6">
                <label class="block text-sm text-gray-300 mb-1">Review (Opsional)</label>
                <textarea name="review" rows="3" placeholder="Bagaimana pengalaman Anda?" class="w-full rounded-lg bg-gray-800 border border-gray-700 text-white px-4 py-2.5"></textarea>
            </div>
            
            {{-- TIP --}}
            <div class="mb-6">
                <label class="block text-sm text-gray-300 mb-1">Tip untuk Driver (Opsional)</label>
                <div class="grid grid-cols-4 gap-2 mb-2">
                    <button type="button" class="tip-btn bg-gray-800 hover:bg-green-600 text-white py-2 rounded-lg" data-tip="5000">Rp 5.000</button>
                    <button type="button" class="tip-btn bg-gray-800 hover:bg-green-600 text-white py-2 rounded-lg" data-tip="10000">Rp 10.000</button>
                    <button type="button" class="tip-btn bg-gray-800 hover:bg-green-600 text-white py-2 rounded-lg" data-tip="15000">Rp 15.000</button>
                    <button type="button" class="tip-btn bg-gray-800 hover:bg-green-600 text-white py-2 rounded-lg" data-tip="20000">Rp 20.000</button>
                </div>
                <input type="number" name="tip" id="tipAmount" class="w-full rounded-lg bg-gray-800 border border-gray-700 text-white px-4 py-2.5" placeholder="Atau masukkan nominal sendiri" step="5000" min="0" max="50000">
            </div>
            
            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-xl transition">
                Kirim Rating & Tip
            </button>
        </form>
        
    </div>
</div>

<script>
    // Star rating
    const stars = document.querySelectorAll('#starRating span');
    const ratingInput = document.getElementById('ratingValue');
    
    stars.forEach(star => {
        star.addEventListener('click', function() {
            const value = this.dataset.value;
            ratingInput.value = value;
            
            stars.forEach((s, i) => {
                if (i < value) {
                    s.innerHTML = '★';
                    s.classList.add('text-yellow-400');
                    s.classList.remove('text-gray-500');
                } else {
                    s.innerHTML = '☆';
                    s.classList.remove('text-yellow-400');
                    s.classList.add('text-gray-500');
                }
            });
        });
    });
    
    // Tip buttons
    document.querySelectorAll('.tip-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('tipAmount').value = this.dataset.tip;
        });
    });
</script>
@endsection