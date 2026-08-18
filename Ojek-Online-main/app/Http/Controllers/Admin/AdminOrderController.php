<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function index()
    {
        $orders = Pesanan::with(['user', 'driver'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        $statusCounts = [
            'pending' => Pesanan::where('status', Pesanan::STATUS_PENDING)->count(),
            'accepted' => Pesanan::where('status', Pesanan::STATUS_ACCEPTED)->count(),
            'on_trip' => Pesanan::where('status', Pesanan::STATUS_ON_TRIP)->count(),
            'completed' => Pesanan::where('status', Pesanan::STATUS_COMPLETED)->count(),
        ];
        
        return view('admin.orders.index', compact('orders', 'statusCounts'));
    }
    
    public function show($id)
    {
        $order = Pesanan::with(['user', 'driver', 'rating'])
            ->findOrFail($id);
        
        return view('admin.orders.show', compact('order'));
    }
    
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,accepted,on_trip,completed,cancelled'
        ]);
        
        $order = Pesanan::findOrFail($id);
        $order->update(['status' => $request->status]);
        
        return redirect()->back()->with('success', 'Status pesanan berhasil diupdate.');
    }
    
    public function destroy($id)
    {
        $order = Pesanan::findOrFail($id);
        $order->delete();
        
        return redirect()->route('admin.orders.index')->with('success', 'Pesanan berhasil dihapus.');
    }
    
    public function export()
    {
        $orders = Pesanan::with(['user', 'driver'])->get();
        return response()->json(['message' => 'Export feature coming soon']);
    }
}