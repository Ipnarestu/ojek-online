<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Pesanan;  // 🔥 TAMBAHKAN INI
use App\Models\Wallet;   // 🔥 TAMBAHKAN INI (jika menggunakan Wallet)
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DriverController extends Controller
{
    // GET /drivers
    public function index()
    {
        return Driver::all();
    }

    // POST /drivers
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string',
            'email' => 'required|email|unique:drivers,email',
            'nomor_hp' => 'required|string',
        ]);

        $driver = Driver::create($validated);

        return response()->json([
            'message' => 'Driver created successfully',
            'data' => $driver
        ], 201);
    }

    // GET /drivers/{id}
    public function show($id)
    {
        $driver = Driver::findOrFail($id);
        return $driver;
    }

    // PUT /drivers/{id}
    public function update(Request $request, $id)
    {
        $driver = Driver::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'sometimes|string',
            'email' => 'sometimes|email|unique:drivers,email,' . $id,
            'nomor_hp' => 'sometimes|string',
        ]);

        $driver->update($validated);

        return response()->json([
            'message' => 'Driver updated successfully',
            'data' => $driver
        ]);
    }

    // DELETE /drivers/{id}
    public function destroy($id)
    {
        $driver = Driver::findOrFail($id);
        $driver->delete();

        return response()->json([
            'message' => 'Driver deleted successfully'
        ]);
    }
    
    public function adminIndex()
    {
        $drivers = Driver::all();
        return view('admin.drivers.index', compact('drivers'));
    }

    public function adminShow($id)
    {
        $driver = Driver::findOrFail($id);
        return view('admin.drivers.show', compact('driver'));
    }

    public function toggleStatus($id)
    {
        $driver = Driver::findOrFail($id);
        $driver->status = $driver->status === 'active' ? 'suspended' : 'active';
        $driver->save();

        return back();
    }

    /**
     * Toggle online/offline driver
     */
    public function toggleOnline(Request $request)
    {
        $driver = Auth::guard('driver')->user();

        $driver->online = !$driver->online;

        if ($driver->online) {
            $driver->work_status = 'available';
        } else {
            $driver->work_status = 'offline';
        }

        $driver->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'online' => $driver->online,
                'message' => $driver->online ? 'Status: Online' : 'Status: Offline'
            ]);
        }

        return back()->with('success', $driver->online ? 'Status: Online' : 'Status: Offline');
    }

    /**
     * Update lokasi driver real-time
     */
    public function updateLocation(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        $driver = Auth::guard('driver')->user();
        $driver->update([
            'lat' => $request->lat,
            'lng' => $request->lng,
        ]);

        return response()->json([
            'success' => true,
            'lat' => $driver->lat,
            'lng' => $driver->lng,
        ]);
    }

    /**
     * Get nearby drivers for user (API)
     */
    public function getNearbyDrivers(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'radius' => 'nullable|numeric|min:1|max:20'
        ]);

        $radius = $request->radius ?? 5;

        $drivers = Driver::where('online', true)
            ->where('status', 'aktif')
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
            ->get();

        return response()->json([
            'success' => true,
            'count' => $drivers->count(),
            'drivers' => $drivers->map(function($driver) {
                return [
                    'id' => $driver->id,
                    'name' => $driver->name,
                    'phone' => $driver->phone,
                    'distance' => round($driver->distance, 2),
                    'rating' => $driver->rating,
                    'vehicle_plate' => $driver->vehicle_plate,
                    'vehicle_type' => $driver->vehicle_type
                ];
            })
        ]);
    }

    /**
     * Dashboard driver
     */
  public function dashboard()
{
    $driver = Auth::guard('driver')->user();
    
    // Pendapatan bersih driver hari ini (92% dari harga)
    $todayEarnings = Pesanan::where('driver_id', $driver->id)
        ->where('status', Pesanan::STATUS_COMPLETED)
        ->whereDate('completed_at', today())
        ->sum('driver_earning');  // ← Gunakan driver_earning
    
    // Total pesanan selesai
    $totalOrders = Pesanan::where('driver_id', $driver->id)
        ->where('status', Pesanan::STATUS_COMPLETED)
        ->count();
    
    // Pesanan aktif
    $activeOrder = Pesanan::where('driver_id', $driver->id)
        ->whereIn('status', [Pesanan::STATUS_ACCEPTED, Pesanan::STATUS_ON_TRIP])
        ->first();
    
    // Statistik komisi hari ini (opsional)
    $todayStats = (object) [
        'daily_orders_count' => Pesanan::where('driver_id', $driver->id)
            ->where('status', Pesanan::STATUS_COMPLETED)
            ->whereDate('completed_at', today())
            ->count(),
        'daily_total_earnings' => $todayEarnings,
        'daily_admin_fee' => Pesanan::where('driver_id', $driver->id)
            ->where('status', Pesanan::STATUS_COMPLETED)
            ->whereDate('completed_at', today())
            ->sum('admin_fee'),
    ];
    
    return view('driver.dashboard', compact('driver', 'todayEarnings', 'totalOrders', 'activeOrder', 'todayStats'));
}
}