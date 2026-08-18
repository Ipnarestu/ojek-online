<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Driver;
use App\Models\Wallet;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DriverRegisterController extends Controller
{
    public function show()
    {
        return view('auth.driver-register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:drivers,email',
            'phone'    => 'required|string|max:20|unique:drivers,phone',
            'password' => 'required|min:6',
            'ktp_path' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'sim_path' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'stnk_path' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'photo_path' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Upload dokumen
        $ktpPath = $request->file('ktp_path')->store('driver_documents/ktp', 'public');
        $simPath = $request->file('sim_path')->store('driver_documents/sim', 'public');
        $stnkPath = $request->file('stnk_path')->store('driver_documents/stnk', 'public');
        $photoPath = $request->file('photo_path')->store('driver_documents/photos', 'public');

        // 1. Create driver dengan status pending verifikasi
        $driver = Driver::create([
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'phone'       => $validated['phone'],
            'password'    => Hash::make($validated['password']),
            'status'      => 'pending',
            'online'      => 0,
            'work_status' => 'offline',
            'verification_status' => 'pending',
            'ktp_path' => $ktpPath,
            'sim_path' => $simPath,
            'stnk_path' => $stnkPath,
            'photo_path' => $photoPath,
        ]);

        // 2. Auto create wallet (DRIVER)
        Wallet::create([
            'owner_type' => Driver::class,
            'owner_id'   => $driver->id,
            'balance'    => 0,
            'currency'   => 'IDR',
        ]);

        return redirect()
            ->route('driver.login')
            ->with('success', 'Registrasi berhasil! Akun Anda akan diverifikasi oleh admin. Status akan dikirim ke email Anda.');
    }
}