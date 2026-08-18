<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Wallet;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserOrderController extends Controller
{
    /**
     * Tampilkan form order berdasarkan tipe
     */
    public function orderForm($type)
    {
        return view('user.order-form', compact('type'));
    }
    
    /**
     * Submit pesanan baru - Pilih 1 driver secara acak
     */
    public function submitOrder(Request $request)
    {
        $request->validate([
            'type'           => 'required|in:ride,delivery,shopping',
            'pickup'         => 'required|string',
            'pickup_lat'     => 'required|numeric',
            'pickup_lng'     => 'required|numeric',
            'destination'    => 'required|string',
            'dest_lat'       => 'required|numeric',
            'dest_lng'       => 'required|numeric',
            'price'          => 'required|numeric|min:0',
            'note'           => 'nullable|string',
            'payment_method' => 'required|in:cod,qris,wallet',
        ]);
        
        $user = Auth::user();
        
        // Jika payment_method = wallet, cek saldo
        if ($request->payment_method === 'wallet') {
            $wallet = Wallet::getOrCreateFor($user);
            if ($wallet->balance < $request->price) {
                return redirect()->back()->with('error', 'Saldo tidak mencukupi. Silakan top up terlebih dahulu.');
            }
        }
        
        // 🔥 PERUBAHAN: Cari driver aktif TERLEBIH DAHULU
        $nearbyDrivers = $this->findNearbyDrivers($request->pickup_lat, $request->pickup_lng, 5);
        
        // 🔥 PERUBAHAN: Pilih 1 driver secara ACAK
        $selectedDriverId = null;
        $status = Pesanan::STATUS_PENDING;
        
        if ($nearbyDrivers->isNotEmpty()) {
            // Ambil 1 driver secara acak (bukan yang terdekat)
            $selectedDriver = $nearbyDrivers->random();
            $selectedDriverId = $selectedDriver->id;
            $status = Pesanan::STATUS_ACCEPTED; // Langsung accepted karena sudah diassign
            
            Log::info('Driver dipilih untuk order', [
                'user_id' => $user->id,
                'selected_driver_id' => $selectedDriverId,
                'total_drivers_available' => $nearbyDrivers->count(),
                'all_drivers' => $nearbyDrivers->pluck('id')->toArray()
            ]);
            
            // Update status driver menjadi busy
            Driver::where('id', $selectedDriverId)->update(['work_status' => 'busy']);
        }
        
        // Create order dengan driver yang sudah dipilih
        $pesanan = Pesanan::create([
            'user_id'         => $user->id,
            'driver_id'       => $selectedDriverId, // 🔥 SEKARANG TERISI
            'pickup_location' => $request->pickup,
            'pickup_lat'      => $request->pickup_lat,
            'pickup_lng'      => $request->pickup_lng,
            'destination'     => $request->destination,
            'dest_lat'        => $request->dest_lat ?? null,
            'dest_lng'        => $request->dest_lng ?? null,
            'pickup_note'     => $request->note,
            'price'           => $request->price,
            'payment_method'  => $request->payment_method,
            'payment_status'  => 'pending',
            'status'          => $status, // 🔥 ACCEPTED jika ada driver
            'user_notified'   => false,
        ]);
        
        // Jika payment_method = wallet, langsung debit saldo
        if ($request->payment_method === 'wallet') {
            $wallet = Wallet::getOrCreateFor($user);
            $wallet->debit($request->price, 'payment', 'Pembayaran order #' . $pesanan->id);
            $pesanan->update(['payment_status' => 'paid']);
        }
        
        // Jika tidak ada driver sama sekali
        if ($nearbyDrivers->isEmpty()) {
            return redirect()->route('user.order.track', $pesanan->id)
                ->with('warning', '⏳ Pesanan dibuat, namun belum ada driver tersedia. Kami akan mencari driver untuk Anda.');
        }
        
        // 🔥 SUCCESS: Ada driver yang ditugaskan
        return redirect()->route('user.order.track', $pesanan->id)
            ->with('success', '✅ Pesanan berhasil dibuat! Driver ' . $selectedDriver->name . ' sedang menuju lokasi Anda.');
    }
    
    /**
     * Cari driver terdekat berdasarkan lokasi
     */
    private function findNearbyDrivers($lat, $lng, $radiusKm = 5)
    {
        return Driver::where('online', true)
            ->where('status', 'aktif')
            ->where('work_status', 'free') // Hanya driver yang sedang free
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->selectRaw('*, (
                6371 * acos(
                    cos(radians(?)) * 
                    cos(radians(lat)) * 
                    cos(radians(lng) - radians(?)) + 
                    sin(radians(?)) * 
                    sin(radians(lat))
                )
            ) AS distance', [$lat, $lng, $lat])
            ->having('distance', '<=', $radiusKm)
            ->orderBy('distance', 'asc')
            ->get();
    }
    
    /**
     * Tracking pesanan
     */
    public function trackOrder($id)
    {
        $user = Auth::user();
        $pesanan = Pesanan::with('driver')->where('user_id', $user->id)->findOrFail($id);
        
        return view('user.order-tracking', compact('pesanan'));
    }
    
    /**
     * API untuk mencari driver terdekat (AJAX)
     */
    public function findNearbyDriversApi(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'radius' => 'nullable|numeric|min:1|max:20'
        ]);
        
        $radius = $request->radius ?? 5;
        
        $drivers = Driver::where('online', true)
            ->where('status', 'aktif')
            ->where('work_status', 'free')
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->selectRaw('*, (
                6371 * acos(
                    cos(radians(?)) * 
                    cos(radians(lat)) * 
                    cos(radians(lng) - radians(?)) + 
                    sin(radians(?)) * 
                    sin(radians(lat))
                )
            ) AS distance', [$request->lat, $request->lng, $request->lat])
            ->having('distance', '<=', $radius)
            ->orderBy('distance', 'asc')
            ->limit(10)
            ->get();
        
        return response()->json([
            'success' => true,
            'count' => $drivers->count(),
            'drivers' => $drivers->map(function($driver) {
                return [
                    'id' => $driver->id,
                    'name' => $driver->name,
                    'distance' => round($driver->distance, 2),
                    'rating' => $driver->rating,
                    'vehicle_plate' => $driver->vehicle_plate
                ];
            })
        ]);
    }
    
    /**
     * History pesanan user
     */
    public function orderHistory()
    {
        $user = Auth::user();
        $orders = Pesanan::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('user.order-history', compact('orders'));
    }
    
    /**
     * Menampilkan halaman rating untuk pesanan yang sudah selesai
     */
    public function ratingForm($id)
    {
        $user = Auth::user();
        
        $pesanan = Pesanan::where('id', $id)
            ->where('user_id', $user->id)
            ->where('status', Pesanan::STATUS_COMPLETED)
            ->with('driver')
            ->firstOrFail();
        
        // Cek apakah sudah pernah rating
        $existingRating = \App\Models\Rating::where('pesanan_id', $id)->first();
        if ($existingRating) {
            return redirect()->route('user.orders')
                ->with('warning', 'Anda sudah memberikan rating untuk pesanan ini.');
        }
        
        return view('user.rating', compact('pesanan'));
    }
    
    /**
     * Simpan rating dari user
     */
    public function storeRating(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:500'
        ]);
        
        $user = Auth::user();
        
        $pesanan = Pesanan::where('id', $id)
            ->where('user_id', $user->id)
            ->where('status', Pesanan::STATUS_COMPLETED)
            ->firstOrFail();
        
        // Cek apakah sudah pernah rating
        $existingRating = \App\Models\Rating::where('pesanan_id', $id)->first();
        if ($existingRating) {
            return redirect()->route('user.orders')
                ->with('warning', 'Anda sudah memberikan rating untuk pesanan ini.');
        }
        
        // Simpan rating
        $rating = \App\Models\Rating::create([
            'pesanan_id' => $pesanan->id,
            'user_id' => $user->id,
            'driver_id' => $pesanan->driver_id,
            'rating' => $request->rating,
            'review' => $request->review
        ]);
        
        // Update rata-rata rating driver
        if ($pesanan->driver_id) {
            $avgRating = \App\Models\Rating::where('driver_id', $pesanan->driver_id)->avg('rating');
            Driver::where('id', $pesanan->driver_id)->update(['rating' => round($avgRating, 1)]);
        }
        
        return redirect()->route('user.orders')
            ->with('success', 'Terima kasih atas rating dan review Anda!');
    }
}