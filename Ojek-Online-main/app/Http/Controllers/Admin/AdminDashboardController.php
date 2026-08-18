<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Driver;
use App\Models\Pesanan;
use App\Models\Rating;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // ======================
        // KPI DASAR
        // ======================
        $totalUsers = User::count();
        $activeUsers = User::where('status', 'aktif')->count();

        $totalDrivers = Driver::count();
        $onlineDrivers = Driver::where('online', 1)->count();
        $offlineDrivers = Driver::where('online', 0)->count();

        $totalOrders = Pesanan::count();
        $completedOrders = Pesanan::where('status', Pesanan::STATUS_COMPLETED)->count();

        // ======================
        // PENDAPATAN KOMISI ADMIN (8%)
        // ======================
        $totalAdminCommission = Pesanan::where('status', Pesanan::STATUS_COMPLETED)->sum('admin_fee');
        $totalDriverEarnings = Pesanan::where('status', Pesanan::STATUS_COMPLETED)->sum('driver_earning');
        $totalRevenue = Pesanan::where('status', Pesanan::STATUS_COMPLETED)->sum('price');
        
        $todayAdminCommission = Pesanan::where('status', Pesanan::STATUS_COMPLETED)
            ->whereDate('completed_at', today())
            ->sum('admin_fee');
        
        $monthlyAdminCommission = Pesanan::where('status', Pesanan::STATUS_COMPLETED)
            ->whereMonth('completed_at', now()->month)
            ->sum('admin_fee');

        // ======================
        // RECENT ACTIVITY
        // ======================
        $recentOrders = Pesanan::latest()->limit(5)->get();
        $recentUsers = User::latest()->limit(5)->get();
        $recentDrivers = Driver::latest()->limit(5)->get();

        // ======================
        // CHART DATA
        // ======================
        $orderPending = Pesanan::where('status', Pesanan::STATUS_PENDING)->count();
        $orderAccepted = Pesanan::where('status', Pesanan::STATUS_ACCEPTED)->count();
        $orderCompleted = Pesanan::where('status', Pesanan::STATUS_COMPLETED)->count();

        // ======================
        // TOP STATISTIK
        // ======================

        // 🔝 TOP RATING DRIVER
        $topRatedRaw = Rating::select('driver_id', DB::raw('AVG(rating) as avg_rating'))
            ->whereNotNull('driver_id')
            ->groupBy('driver_id')
            ->orderByDesc('avg_rating')
            ->first();

        $topRatedDriver = null;
        if ($topRatedRaw) {
            $driver = Driver::find($topRatedRaw->driver_id);
            if ($driver) {
                $topRatedDriver = (object) [
                    'name' => $driver->name,
                    'avg_rating' => round($topRatedRaw->avg_rating, 1),
                ];
            }
        }

        // 🔝 DRIVER DENGAN ORDER TERBANYAK
        $mostActiveRaw = Driver::withCount('pesanans')->orderByDesc('pesanans_count')->first();
        $mostActiveDriver = null;
        if ($mostActiveRaw) {
            $mostActiveDriver = (object) [
                'name' => $mostActiveRaw->name,
                'total_orders' => $mostActiveRaw->pesanans_count,
            ];
        }

        // 🔝 TOP EARNING DRIVER (berdasarkan driver_earning)
        $topEarningDriver = Pesanan::where('status', Pesanan::STATUS_COMPLETED)
            ->select('driver_id', DB::raw('SUM(driver_earning) as total_earning'))
            ->groupBy('driver_id')
            ->orderByDesc('total_earning')
            ->with('driver')
            ->first();

        $topEarningDriverData = null;
        if ($topEarningDriver && $topEarningDriver->driver) {
            $topEarningDriverData = (object) [
                'name' => $topEarningDriver->driver->name,
                'total_earning' => $topEarningDriver->total_earning,
            ];
        }

        // ======================
        // NILAI DEFAULT (JIKA TIDAK ADA DATA)
        // ======================
        if (!$topRatedDriver) {
            $topRatedDriver = (object) [
                'name' => '-',
                'avg_rating' => 0,
            ];
        }

        if (!$mostActiveDriver) {
            $mostActiveDriver = (object) [
                'name' => '-',
                'total_orders' => 0,
            ];
        }

        if (!$topEarningDriverData) {
            $topEarningDriverData = (object) [
                'name' => '-',
                'total_earning' => 0,
            ];
        }

        // ======================
        // RETURN KE VIEW
        // ======================
        return view('admin.dashboard', compact(
            'totalUsers', 'activeUsers',
            'totalDrivers', 'onlineDrivers', 'offlineDrivers',
            'totalOrders', 'completedOrders',
            'totalAdminCommission', 'totalDriverEarnings', 'totalRevenue',
            'todayAdminCommission', 'monthlyAdminCommission',
            'recentOrders', 'recentUsers', 'recentDrivers',
            'orderPending', 'orderAccepted', 'orderCompleted',
            'topRatedDriver', 'mostActiveDriver', 'topEarningDriverData'
        ));
    }
}