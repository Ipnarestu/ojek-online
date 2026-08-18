<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Rating;
use App\Models\Wallet;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    /**
     * Menampilkan form rating
     */
    public function create($pesananId)
    {
        $user = Auth::user();
        
        $pesanan = Pesanan::where('id', $pesananId)
            ->where('user_id', $user->id)
            ->where('status', Pesanan::STATUS_COMPLETED)
            ->with('driver')
            ->firstOrFail();
        
        // Cek apakah sudah pernah rating
        $existingRating = Rating::where('pesanan_id', $pesananId)->first();
        if ($existingRating) {
            return redirect()->route('user.orders')
                ->with('warning', 'Anda sudah memberikan rating untuk pesanan ini.');
        }
        
        return view('user.rating', compact('pesanan'));
    }
    
    /**
     * Menyimpan rating (sesuai dengan form di rating.blade.php)
     */
    public function store(Request $request, $pesananId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:500',
            'tip' => 'nullable|numeric|min:0|max:50000',
        ]);
        
        $user = Auth::user();
        
        $pesanan = Pesanan::where('id', $pesananId)
            ->where('user_id', $user->id)
            ->where('status', Pesanan::STATUS_COMPLETED)
            ->firstOrFail();
        
        // Cek apakah sudah pernah rating
        $existingRating = Rating::where('pesanan_id', $pesananId)->first();
        if ($existingRating) {
            return redirect()->route('user.orders')
                ->with('warning', 'Anda sudah memberikan rating untuk pesanan ini.');
        }
        
        // Simpan rating
        $rating = Rating::create([
            'pesanan_id' => $pesanan->id,
            'user_id' => $user->id,
            'driver_id' => $pesanan->driver_id,
            'rating' => $request->rating,
            'comment' => $request->review,  // field 'review' dari form → 'comment' di database
            'tip' => $request->tip ?? 0,
        ]);
        
        // Jika ada tip, tambahkan ke wallet driver
        if ($request->tip > 0 && $pesanan->driver_id) {
            $wallet = Wallet::firstOrCreate(
                [
                    'owner_type' => Driver::class,
                    'owner_id' => $pesanan->driver_id,
                ],
                [
                    'balance' => 0,
                    'currency' => 'IDR',
                ]
            );
            $wallet->credit($request->tip, 'tip', 'Tip untuk pesanan #' . $pesanan->id);
            
            // Update tip_amount di pesanan
            $pesanan->update(['tip_amount' => $request->tip]);
        }
        
        // Update rating rata-rata driver
        if ($pesanan->driver_id) {
            $averageRating = Rating::where('driver_id', $pesanan->driver_id)->avg('rating');
            Driver::where('id', $pesanan->driver_id)->update(['rating' => round($averageRating, 1)]);
        }
        
        return redirect()->route('user.orders')
            ->with('success', 'Terima kasih atas rating dan tip Anda!');
    }
}