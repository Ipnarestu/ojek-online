<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\DriverVerifiedMail;
use App\Mail\DriverRejectedMail;

class AdminDriverController extends Controller
{
    public function index(Request $request)
    {
        $query = Driver::with('kendaraan');
        
        if ($request->filled('status')) {
            $status = $request->status;
            if ($status == 'pending') {
                $query->where('verification_status', 'pending');
            } elseif ($status == 'approved') {
                $query->where('verification_status', 'approved');
            } elseif ($status == 'rejected') {
                $query->where('verification_status', 'rejected');
            }
        }
        
        $drivers = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('admin.drivers.index', compact('drivers'));
    }

    public function show($id)
    {
        $driver = Driver::with('kendaraan')->findOrFail($id);
        return view('admin.drivers.show', compact('driver'));
    }

    public function verify($id)
    {
        $driver = Driver::findOrFail($id);
        
        $driver->update([
            'verification_status' => 'approved',
            'verified_at' => now(),
            'verified_by' => Auth::guard('admin')->id(),
            'status' => 'aktif',
        ]);

        // Kirim email notifikasi (opsional)
        try {
            Mail::to($driver->email)->send(new DriverVerifiedMail($driver));
        } catch (\Exception $e) {
            \Log::error('Gagal mengirim email verifikasi: ' . $e->getMessage());
        }

        return redirect()->route('admin.drivers.index')
            ->with('success', 'Driver berhasil diverifikasi!');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $driver = Driver::findOrFail($id);
        
        $driver->update([
            'verification_status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'verified_by' => Auth::guard('admin')->id(),
            'status' => 'nonaktif',
        ]);

        try {
            Mail::to($driver->email)->send(new DriverRejectedMail($driver, $request->rejection_reason));
        } catch (\Exception $e) {
            \Log::error('Gagal mengirim email penolakan: ' . $e->getMessage());
        }

        return redirect()->route('admin.drivers.index')
            ->with('success', 'Driver ditolak.');
    }

    public function toggle($id)
    {
        $driver = Driver::findOrFail($id);
        $driver->update(['online' => $driver->online ? 0 : 1]);
        return back()->with('success', 'Status driver diperbarui.');
    }

    public function orders(Request $request, $id)
    {
        $driver = Driver::findOrFail($id);
        $query = Pesanan::where('driver_id', $driver->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->period === 'today') {
            $query->whereDate('created_at', today());
        } elseif ($request->period === 'week') {
            $query->where('created_at', '>=', now()->subDays(7));
        } elseif ($request->period === 'month') {
            $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        return view('admin.drivers.orders', compact('driver', 'orders'));
    }

    public function downloadDocument($id, $type)
    {
        $driver = Driver::findOrFail($id);
        
        $allowedTypes = ['ktp_path', 'sim_path', 'stnk_path', 'photo_path'];
        if (!in_array($type, $allowedTypes)) {
            abort(404);
        }
        
        $path = $driver->$type;
        if (!$path || !Storage::disk('public')->exists($path)) {
            abort(404);
        }
        
        return Storage::disk('public')->download($path);
    }
}