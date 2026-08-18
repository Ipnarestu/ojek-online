<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Pesanan;
use App\Models\Wallet;
use App\Models\Driver;

class DriverOrderController extends Controller
{
    /**
     * List order untuk driver
     */
    public function index()
    {
        /** @var \App\Models\Driver $driver */
        $driver = Auth::guard('driver')->user();

        // Pesanan yang tersedia (pending, belum ada driver)
        $availableOrders = Pesanan::where('status', Pesanan::STATUS_PENDING)
            ->whereNull('driver_id')
            ->orderBy('created_at', 'asc')
            ->get();

        // Pesanan aktif yang sedang ditangani driver
        $activeOrders = Pesanan::where('driver_id', $driver->id)
            ->whereIn('status', [Pesanan::STATUS_ACCEPTED, Pesanan::STATUS_ON_TRIP])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('driver.orders', compact('availableOrders', 'activeOrders'));
    }

    /**
     * Driver menerima order (via AJAX dari notifikasi)
     */
    public function accept($id)
    {
        /** @var \App\Models\Driver $driver */
        $driver = Auth::guard('driver')->user();

        if ($driver->online != 1) {
            return response()->json([
                'success' => false,
                'message' => 'Driver harus online terlebih dahulu.'
            ]);
        }

        try {
            DB::transaction(function () use ($driver, $id) {
                $order = Pesanan::lockForUpdate()
                    ->where('id', $id)
                    ->whereNull('driver_id')
                    ->where('status', Pesanan::STATUS_PENDING)
                    ->firstOrFail();

                $order->update([
                    'driver_id' => $driver->id,
                    'status' => Pesanan::STATUS_ACCEPTED,
                    'user_notified' => 1,
                ]);

                $driver->update(['work_status' => 'busy']);
            });

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil diterima'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Detail pesanan untuk driver (dengan peta)
     */
    public function getOrderDetail($id)
    {
        /** @var \App\Models\Driver $driver */
        $driver = Auth::guard('driver')->user();

        $order = Pesanan::where('driver_id', $driver->id)
            ->with('user')
            ->findOrFail($id);
        
        // Hitung rincian pendapatan
        $commissionPercentage = 8;
        $adminFee = $order->price * $commissionPercentage / 100;
        $driverEarning = $order->price - $adminFee;
        
        return view('driver.order-detail', compact('order', 'adminFee', 'driverEarning', 'commissionPercentage'));
    }

    /**
     * Tampilkan halaman rute menuju titik jemput
     */
    public function pickupRoute($id)
    {
        /** @var \App\Models\Driver $driver */
        $driver = Auth::guard('driver')->user();

        $order = Pesanan::where('driver_id', $driver->id)
            ->where('status', Pesanan::STATUS_ACCEPTED)
            ->with('user')
            ->findOrFail($id);

        return view('driver.pickup-route', compact('order'));
    }

    /**
     * Tampilkan halaman antar penumpang (tujuan)
     */
    public function deliveryRoute($id)
    {
        /** @var \App\Models\Driver $driver */
        $driver = Auth::guard('driver')->user();

        $order = Pesanan::where('driver_id', $driver->id)
            ->where('status', Pesanan::STATUS_ON_TRIP)
            ->with('user')
            ->findOrFail($id);

        return view('driver.delivery-route', compact('order'));
    }

    /**
     * Tampilkan halaman rute dari jemput ke tujuan (Google Maps)
     */
    public function orderRoute($id)
    {
        /** @var \App\Models\Driver $driver */
        $driver = Auth::guard('driver')->user();

        $order = Pesanan::where('driver_id', $driver->id)
            ->whereIn('status', [Pesanan::STATUS_ACCEPTED, Pesanan::STATUS_ON_TRIP])
            ->with('user')
            ->findOrFail($id);

        return view('driver.order-route', compact('order'));
    }

    /**
     * Driver memulai perjalanan (sudah sampai jemput)
     */
    public function startRide($id)
    {
        /** @var \App\Models\Driver $driver */
        $driver = Auth::guard('driver')->user();

        $order = Pesanan::where('driver_id', $driver->id)
            ->where('status', Pesanan::STATUS_ACCEPTED)
            ->firstOrFail();

        $order->update(['status' => Pesanan::STATUS_ON_TRIP]);

        return redirect()
            ->route('driver.order.detail', $id)
            ->with('success', 'Perjalanan dimulai! Antar penumpang ke tujuan.');
    }

    /**
     * Driver menyelesaikan tahap jemput (status berubah dari accepted ke on_trip)
     */
    public function completePickup($id)
    {
        /** @var \App\Models\Driver $driver */
        $driver = Auth::guard('driver')->user();

        $order = Pesanan::where('driver_id', $driver->id)
            ->where('status', Pesanan::STATUS_ACCEPTED)
            ->firstOrFail();

        $order->update(['status' => Pesanan::STATUS_ON_TRIP]);

        return redirect()
            ->route('driver.order.delivery', $order->id)
            ->with('success', 'Penumpang sudah dijemput! Silakan lanjutkan ke tujuan.');
    }

    /**
 * Driver menyelesaikan order
 */
public function complete($id)
{
    try {
        /** @var \App\Models\Driver $driver */
        $driver = Auth::guard('driver')->user();
        
        $order = Pesanan::find($id);
        
        if (!$order) {
            return redirect()->back()->with('error', 'Pesanan tidak ditemukan');
        }
        
        // Hitung komisi flat 8%
        $commissionPercentage = 8;
        $adminFee = $order->price * $commissionPercentage / 100;
        $driverEarning = $order->price - $adminFee;
        
        // Update pesanan
        $order->status = Pesanan::STATUS_COMPLETED;
        $order->completed_at = now();
        $order->admin_fee = $adminFee;
        $order->driver_earning = $driverEarning;
        $order->save();
        
        // Tambah saldo driver ke wallet
        $wallet = Wallet::firstOrCreate(
            [
                'owner_type' => Driver::class,
                'owner_id'   => $driver->id,
            ],
            [
                'balance'  => 0,
                'currency' => 'IDR',
            ]
        );
        $wallet->credit($driverEarning, 'order_income', 'Pendapatan dari pesanan #' . $order->id);
        
        // Redirect ke halaman konfirmasi
        return redirect()->route('driver.order.done', $order->id)
            ->with('success', 'Pesanan selesai! +Rp ' . number_format($driverEarning, 0, ',', '.'));
        
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}

    /**
     * Menampilkan halaman konfirmasi penyelesaian order
     */
    public function completeConfirmation($id)
    {
        $order = Pesanan::with('user')->find($id);
        
        if (!$order) {
            abort(404, 'Pesanan tidak ditemukan');
        }
        
        return view('driver.order-complete', compact('order'));
    }

    /**
     * Map pickup untuk driver (Leaflet - OpenStreetMap)
     */
    public function pickupMap($id)
    {
        /** @var \App\Models\Driver $driver */
        $driver = Auth::guard('driver')->user();

        $order = Pesanan::where('id', $id)
            ->where('driver_id', $driver->id)
            ->firstOrFail();

        return view('driver.pickup-map', compact('order'));
    }

    /**
     * Riwayat pesanan driver
     */
    public function orderHistory()
    {
        /** @var \App\Models\Driver $driver */
        $driver = Auth::guard('driver')->user();

        $orders = Pesanan::where('driver_id', $driver->id)
            ->where('status', Pesanan::STATUS_COMPLETED)
            ->orderBy('completed_at', 'desc')
            ->paginate(10);

        $totalEarnings = Pesanan::where('driver_id', $driver->id)
            ->where('status', Pesanan::STATUS_COMPLETED)
            ->sum('driver_earning');

        $totalTips = Pesanan::where('driver_id', $driver->id)
            ->where('status', Pesanan::STATUS_COMPLETED)
            ->sum('tip_amount');

        return view('driver.order-history', compact('orders', 'totalEarnings', 'totalTips'));
    }

    /**
     * Pendapatan hari ini (API)
     */
    public function dailyEarnings()
    {
        /** @var \App\Models\Driver $driver */
        $driver = Auth::guard('driver')->user();

        $todayEarnings = Pesanan::where('driver_id', $driver->id)
            ->where('status', Pesanan::STATUS_COMPLETED)
            ->whereDate('completed_at', today())
            ->sum('d');

        $todayOrders = Pesanan::where('driver_id', $driver->id)
            ->where('status', Pesanan::STATUS_COMPLETED)
            ->whereDate('completed_at', today())
            ->count();

        return response()->json([
            'success' => true,
            'earnings' => $todayEarnings,
            'formatted_earnings' => 'Rp ' . number_format($todayEarnings, 0, ',', '.'),
            'orders_count' => $todayOrders,
        ]);
    }

    /**
     * Statistik pesanan driver
     */
    public function orderStats()
{
    /** @var \App\Models\Driver $driver */
    $driver = Auth::guard('driver')->user();

    $stats = [
        'total_orders' => Pesanan::where('driver_id', $driver->id)->count(),
        'completed_orders' => Pesanan::where('driver_id', $driver->id)->where('status', Pesanan::STATUS_COMPLETED)->count(),
        'cancelled_orders' => Pesanan::where('driver_id', $driver->id)->where('status', Pesanan::STATUS_CANCELLED)->count(),
        'total_earnings' => Pesanan::where('driver_id', $driver->id)->where('status', Pesanan::STATUS_COMPLETED)->sum('driver_earning'),  // ← GANTI price → driver_earning
        'total_tips' => Pesanan::where('driver_id', $driver->id)->where('status', Pesanan::STATUS_COMPLETED)->sum('tip_amount'),
        'average_rating' => round($driver->rating ?? 0, 1),
    ];

    return view('driver.order-stats', compact('stats'));
}

    /**
     * Cek pesanan baru untuk notifikasi (API)
     */
    public function checkNewOrders()
    {       
        /** @var \App\Models\Driver $driver */
        $driver = Auth::guard('driver')->user();
        
        $newOrder = Pesanan::where('status', Pesanan::STATUS_PENDING)
            ->whereNull('driver_id')
            ->orderBy('created_at', 'desc')
            ->first();

        if ($newOrder) {
            return response()->json([
                'has_new_order' => true,
                'order' => [
                    'id' => $newOrder->id,
                    'pickup_location' => $newOrder->pickup_location,
                    'destination' => $newOrder->destination,
                    'price' => $newOrder->price,
                ]
            ]);
        }

        return response()->json(['has_new_order' => false]);
    }
}