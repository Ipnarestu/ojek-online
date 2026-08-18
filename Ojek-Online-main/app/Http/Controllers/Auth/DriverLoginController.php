<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DriverLoginController extends Controller
{
    /**
     * Tampilkan form login driver
     */
    public function showLoginForm()
    {
        // Jika sudah login, redirect ke dashboard
        if (Auth::guard('driver')->check()) {
            return redirect()->route('driver.dashboard');
        }
        
        return view('auth.driver-login');
    }

    /**
     * Proses login driver
     */
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        // Attempt login dengan remember me
        if (Auth::guard('driver')->attempt($credentials, $request->filled('remember'))) {
            // Regenerate session untuk security
            $request->session()->regenerate();
            
            // Dapatkan driver yang login
            $driver = Auth::guard('driver')->user();
            
            // Log aktivitas login
            Log::info('Driver logged in', [
                'driver_id' => $driver->id,
                'email' => $driver->email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            // Update last login timestamp (jika kolom ada di database)
            // $driver->update(['last_login_at' => now(), 'last_login_ip' => $request->ip()]);
            
            // 🔥 Redirect ke dashboard driver
            return redirect()->intended(route('driver.dashboard'));
        }

        // Log percobaan login gagal
        Log::warning('Failed driver login attempt', [
            'email' => $request->email,
            'ip' => $request->ip()
        ]);

        // Kembali dengan error
        return back()
            ->withErrors([
                'email' => 'Email atau password yang Anda masukkan salah.',
            ])
            ->withInput($request->only('email', 'remember'))
            ->with('error', 'Login gagal! Periksa kembali email dan password Anda.');
    }

    /**
     * Proses logout driver
     */
    public function logout(Request $request)
    {
        $driver = Auth::guard('driver')->user();
        
        // Log aktivitas logout
        if ($driver) {
            Log::info('Driver logged out', [
                'driver_id' => $driver->id,
                'email' => $driver->email
            ]);
            
            // Update status online menjadi offline (opsional)
            // $driver->update(['online' => false, 'work_status' => 'offline']);
        }
        
        // Logout dari guard driver
        Auth::guard('driver')->logout();
        
        // Invalidate session
        $request->session()->invalidate();
        
        // Regenerate token CSRF
        $request->session()->regenerateToken();
        
        return redirect()->route('driver.login')
            ->with('success', 'Anda telah berhasil logout.');
    }

    /**
     * Tampilkan form lupa password (opsional)
     */
    public function showForgotForm()
    {
        return view('auth.driver-forgot-password');
    }

    /**
     * Kirim link reset password (opsional)
     */
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        // Implementasi reset password sesuai kebutuhan
        // Biasanya menggunakan Password::broker('drivers')->sendResetLink()
        
        return back()->with('success', 'Link reset password telah dikirim ke email Anda.');
    }
}