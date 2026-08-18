<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KendaraanController extends Controller
{
    /* ===============================
     | DRIVER: TAMPILAN KENDARAAN
     =============================== */
    public function driverIndex()
    {
        $driver = Auth::guard('driver')->user();
        
        $kendaraan = Kendaraan::where('driver_id', $driver->id)->first();
        
        return view('driver.kendaraan', compact('kendaraan'));
    }

    /* ===============================
     | DRIVER: CREATE / UPDATE KENDARAAN
     =============================== */
    public function storeOrUpdate(Request $request)
    {
        $driver = Auth::guard('driver')->user();

        $data = $request->validate([
            'Plat_Nomor' => 'required|string|max:255',
            'Tipe'       => 'required|in:Motor,Mobil',
            'Merk'       => 'required|string|max:255',
            'Warna'      => 'required|string|max:255',
            'Tahun'      => 'nullable|integer|min:1990|max:' . date('Y'),
        ]);

        $data['driver_id'] = $driver->id;

        Kendaraan::updateOrCreate(
            ['driver_id' => $driver->id],
            $data
        );

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data kendaraan berhasil disimpan'
            ]);
        }

        return back()->with('success', 'Data kendaraan berhasil disimpan');
    }

    /* ===============================
     | DRIVER: UPDATE KENDARAAN
     =============================== */
    public function update(Request $request, $id)
    {
        $driver = Auth::guard('driver')->user();
        
        $kendaraan = Kendaraan::where('id', $id)
            ->where('driver_id', $driver->id)
            ->firstOrFail();

        $data = $request->validate([
            'Plat_Nomor' => 'required|string|max:255',
            'Tipe'       => 'required|in:Motor,Mobil',
            'Merk'       => 'required|string|max:255',
            'Warna'      => 'required|string|max:255',
            'Tahun'      => 'nullable|integer|min:1990|max:' . date('Y'),
        ]);

        $kendaraan->update($data);

        return back()->with('success', 'Data kendaraan berhasil diupdate');
    }

    /* ===============================
     | DRIVER: HAPUS KENDARAAN
     =============================== */
    public function destroy($id)
    {
        $driver = Auth::guard('driver')->user();
        
        $kendaraan = Kendaraan::where('id', $id)
            ->where('driver_id', $driver->id)
            ->firstOrFail();

        $kendaraan->delete();

        return back()->with('success', 'Data kendaraan berhasil dihapus');
    }

    /* ===============================
     | ADMIN: LIHAT DATA KENDARAAN DRIVER
     =============================== */
    public function adminIndex()
    {
        $kendaraans = Kendaraan::with('driver')->latest()->get();
        return view('admin.kendaraan.index', compact('kendaraans'));
    }

    /* ===============================
     | ADMIN: LIHAT DETAIL KENDARAAN
     =============================== */
    public function show($id)
    {
        $kendaraan = Kendaraan::with('driver')->findOrFail($id);
        return view('admin.kendaraan.show', compact('kendaraan'));
    }

    /* ===============================
     | ADMIN: HAPUS KENDARAAN
     =============================== */
    public function adminDestroy($id)
    {
        $kendaraan = Kendaraan::findOrFail($id);
        $kendaraan->delete();

        return redirect()->route('admin.kendaraan.index')
            ->with('success', 'Data kendaraan berhasil dihapus');
    }

    /* ===============================
     | USER: LIHAT KENDARAAN DRIVER
     =============================== */
    public function showForUser($driver_id)
    {
        $kendaraan = Kendaraan::where('driver_id', $driver_id)->first();
        return view('user.driver-kendaraan', compact('kendaraan'));
    }

    /* ===============================
     | API: GET KENDARAAN DRIVER
     =============================== */
    public function getKendaraanByDriver($driver_id)
    {
        $kendaraan = Kendaraan::where('driver_id', $driver_id)->first();
        
        if ($kendaraan) {
            return response()->json([
                'success' => true,
                'data' => $kendaraan
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Kendaraan tidak ditemukan'
        ]);
    }
}