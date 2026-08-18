<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CONTROLLERS
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\LandingController;

/* AUTH */
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\DriverLoginController;
use App\Http\Controllers\Auth\DriverRegisterController;
use App\Http\Controllers\Auth\AdminLoginController;

/* USER */
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserOrderController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\WalletController;

/* DRIVER */
use App\Http\Controllers\DriverController;
use App\Http\Controllers\DriverOrderController;
use App\Http\Controllers\KendaraanController;

/* ADMIN */
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminDriverController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| LANDING
|--------------------------------------------------------------------------
*/
Route::get('/', [LandingController::class, 'index'])->name('landing');

/*
|--------------------------------------------------------------------------
| AUTH SELECTION (GUEST)
|--------------------------------------------------------------------------
*/
Route::view('/login', 'auth.select-login')->name('login');
Route::view('/register', 'auth.select-register')->name('register.select');
Route::view('/login-select', 'auth.select-login')->name('login.select');

/*
|--------------------------------------------------------------------------
| USER AUTH
|--------------------------------------------------------------------------
*/
Route::get('/login/user', [LoginController::class, 'showUserLoginForm'])->name('user.login');
Route::post('/login/user', [LoginController::class, 'userLogin'])->name('user.login.submit');

Route::get('/register/user', [RegisterController::class, 'showUserRegisterForm'])->name('user.register');
Route::post('/register/user', [RegisterController::class, 'userRegister'])->name('user.register.submit');

Route::post('/logout/user', [LoginController::class, 'logout'])->name('user.logout');

/*
|--------------------------------------------------------------------------
| DRIVER AUTH
|--------------------------------------------------------------------------
*/
Route::get('/login/driver', [DriverLoginController::class, 'showLoginForm'])->name('driver.login');
Route::post('/login/driver', [DriverLoginController::class, 'login'])->name('driver.login.submit');

Route::get('/register/driver', [DriverRegisterController::class, 'show'])->name('driver.register');
Route::post('/register/driver', [DriverRegisterController::class, 'store'])->name('driver.register.submit');

Route::post('/logout/driver', [DriverLoginController::class, 'logout'])->name('driver.logout');

/*
|--------------------------------------------------------------------------
| USER AREA
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('user')->group(function () {

    Route::view('/home', 'user.home')->name('user.home');

    /* ORDER */
    Route::view('/order/select', 'user.order-select')->name('user.order.select');
    Route::get('/order/form/{type}', [UserOrderController::class, 'orderForm'])->name('user.order.form');
    Route::post('/order/submit', [UserOrderController::class, 'submitOrder'])->name('user.order.submit');
    
    /* ORDER MANAGEMENT (TAMBAHAN) */
    Route::get('/orders', [UserOrderController::class, 'orderHistory'])->name('user.orders');
    Route::get('/order/{id}/detail', [UserOrderController::class, 'getOrderDetail'])->name('user.order.detail');
    Route::post('/order/{id}/cancel', [UserOrderController::class, 'cancelOrder'])->name('user.order.cancel');
    Route::get('/order/{id}/track', [UserOrderController::class, 'trackOrder'])->name('user.order.track');
    Route::get('/order/ongoing', [UserOrderController::class, 'getOngoingOrder'])->name('user.order.ongoing');
    Route::post('/order/estimate-price', [UserOrderController::class, 'estimatePrice'])->name('user.order.estimate');
    
    /* ORDER CREATE (ALTERNATIVE) */
    Route::post('/order/create', [UserOrderController::class, 'create'])->name('user.order.create');

    /* MAP & LOCATION */
    Route::view('/pickup/map', 'user.pickup-map')->name('user.pickup.map');
    Route::get('/nearby', [UserController::class, 'nearbyDrivers'])->name('user.nearby');
    Route::post('/find-drivers', [UserController::class, 'findDrivers'])->name('user.find.drivers');
    Route::get('/driver/{id}/map', [UserController::class, 'showDriverMap'])->name('user.driver.map');

    /* KENDARAAN DRIVER (VIEW USER) */
    Route::get('/driver/{id}/kendaraan', [KendaraanController::class, 'showForUser'])
        ->name('user.driver.kendaraan');

    /* RATING & REVIEW */
    Route::get('/orders/{id}/rating', [UserOrderController::class, 'ratingForm'])->name('user.order.rating');
    Route::post('/orders/{id}/rate', [RatingController::class, 'store'])->name('user.order.rate');

    /* WALLET USER */
    Route::get('/wallet', [WalletController::class, 'userWallet'])->name('user.wallet');
    Route::post('/wallet/topup', [WalletController::class, 'topup'])->name('user.wallet.topup');
    Route::get('/wallet/transactions', [WalletController::class, 'userTransactions'])->name('user.wallet.transactions');
});

/*
|--------------------------------------------------------------------------
| DRIVER AREA
|--------------------------------------------------------------------------
*/
Route::middleware('auth:driver')->prefix('driver')->group(function () {
Route::get('/check-new-orders', [DriverOrderController::class, 'checkNewOrders'])->name('driver.check.new.orders');
    Route::get('/dashboard', [DriverController::class, 'dashboard'])->name('driver.dashboard');

    //real-time stats
     Route::get('/get-stats', [DriverController::class, 'getStats'])->name('driver.get.stats');

    /* STATUS & LOCATION */
    Route::post('/toggle-online', [DriverController::class, 'toggleOnline'])->name('driver.toggle.online');
    Route::post('/update-location', [DriverController::class, 'updateLocation'])->name('driver.update.location');
    Route::get('/current-location', [DriverController::class, 'getCurrentLocation'])->name('driver.current.location');

    /* ORDER MANAGEMENT */
    Route::get('/orders', [DriverOrderController::class, 'index'])->name('driver.orders');
    Route::get('/orders/{id}/detail', [DriverOrderController::class, 'getOrderDetail'])->name('driver.order.detail');
    Route::get('/orders/{id}/pickup', [DriverOrderController::class, 'pickupRoute'])->name('driver.order.pickup');
    Route::post('/orders/{id}/pickup-complete', [DriverOrderController::class, 'completePickup'])->name('driver.pickup.complete');
    Route::get('/orders/{id}/delivery', [DriverOrderController::class, 'deliveryRoute'])->name('driver.order.delivery');
    Route::get('/orders/{id}/route', [DriverOrderController::class, 'orderRoute'])->name('driver.order.route');
    
    //
    Route::get('/orders/{id}/complete-test', [DriverOrderController::class, 'complete'])->name('driver.orders.complete.test');
   
    Route::post('/orders/{id}/complete', [DriverOrderController::class, 'complete'])->name('driver.orders.complete');  
    // HALAMAN KONFIRMASI
    Route::get('/orders/{id}/done', [DriverOrderController::class, 'completeConfirmation'])->name('driver.order.done');
    
    Route::post('/orders/{id}/accept', [DriverOrderController::class, 'accept'])->name('driver.orders.accept');
    Route::post('/orders/{id}/reject', [DriverOrderController::class, 'reject'])->name('driver.orders.reject');
    Route::post('/orders/{id}/start', [DriverOrderController::class, 'startRide'])->name('driver.orders.start');
    Route::get('/orders/{id}/pickup-map', [DriverOrderController::class, 'pickupMap'])->name('driver.pickup.map');
    Route::get('/orders/{id}/track', [DriverOrderController::class, 'trackOrder'])->name('driver.order.track');
    
    /* ORDER HISTORY */
    Route::get('/orders/history', [DriverOrderController::class, 'orderHistory'])->name('driver.orders.history');
    Route::get('/orders/stats', [DriverOrderController::class, 'orderStats'])->name('driver.orders.stats');

    /* KENDARAAN */
    Route::get('/kendaraan', [KendaraanController::class, 'driverIndex'])->name('driver.kendaraan.index');
    Route::post('/kendaraan', [KendaraanController::class, 'storeOrUpdate'])->name('driver.kendaraan.save');
    Route::put('/kendaraan/{id}', [KendaraanController::class, 'update'])->name('driver.kendaraan.update');
    Route::delete('/kendaraan/{id}', [KendaraanController::class, 'destroy'])->name('driver.kendaraan.destroy');

    /* WALLET DRIVER */
    Route::get('/wallet', [WalletController::class, 'driverWallet'])->name('driver.wallet');
    Route::post('/wallet/withdraw', [WalletController::class, 'withdraw'])->name('driver.wallet.withdraw');
    Route::get('/wallet/transactions', [WalletController::class, 'driverTransactions'])->name('driver.wallet.transactions');
    
    /* EARNINGS */
    Route::get('/earnings', [DriverOrderController::class, 'earnings'])->name('driver.earnings');
    Route::get('/earnings/daily', [DriverOrderController::class, 'dailyEarnings'])->name('driver.earnings.daily');
});

/*
|--------------------------------------------------------------------------
| ADMIN AUTH
|--------------------------------------------------------------------------
*/
Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminLoginController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

/*
|--------------------------------------------------------------------------
| ADMIN PANEL
|--------------------------------------------------------------------------
*/
Route::middleware('auth:admin')->prefix('admin')->group(function () {

    Route::redirect('/', '/admin/dashboard');

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/dashboard/stats', [AdminDashboardController::class, 'getStats'])->name('admin.dashboard.stats');

    Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('/users/{id}', [AdminUserController::class, 'show'])->name('admin.users.show');
    Route::post('/users/{id}/toggle', [AdminUserController::class, 'toggle'])->name('admin.users.toggle');
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
    Route::post('/users/export', [AdminUserController::class, 'export'])->name('admin.users.export');

    Route::get('/drivers', [AdminDriverController::class, 'index'])->name('admin.drivers.index');
    Route::get('/drivers/{id}', [AdminDriverController::class, 'show'])->name('admin.drivers.show');
    Route::post('/drivers/{id}/toggle', [AdminDriverController::class, 'toggle'])->name('admin.drivers.toggle');
    Route::post('/drivers/{id}/verify', [AdminDriverController::class, 'verify'])->name('admin.drivers.verify');
    Route::delete('/drivers/{id}', [AdminDriverController::class, 'destroy'])->name('admin.drivers.destroy');
    Route::get('/drivers/{id}/orders', [AdminDriverController::class, 'orders'])->name('admin.drivers.orders');
    Route::post('/drivers/export', [AdminDriverController::class, 'export'])->name('admin.drivers.export');

    Route::get('/drivers/{id}/download/{type}', [AdminDriverController::class, 'downloadDocument'])->name('admin.drivers.download');

    Route::get('/orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/orders/{id}', [AdminOrderController::class, 'show'])->name('admin.orders.show');
    Route::post('/orders/{id}/update-status', [AdminOrderController::class, 'updateStatus'])->name('admin.orders.update-status');
    Route::delete('/orders/{id}', [AdminOrderController::class, 'destroy'])->name('admin.orders.destroy');
    Route::post('/orders/export', [AdminOrderController::class, 'export'])->name('admin.orders.export');

    Route::get('/kendaraan', [KendaraanController::class, 'adminIndex'])->name('admin.kendaraan.index');
    Route::get('/kendaraan/{id}', [KendaraanController::class, 'show'])->name('admin.kendaraan.show');
    Route::delete('/kendaraan/{id}', [KendaraanController::class, 'destroy'])->name('admin.kendaraan.destroy');

    Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports.index');
    Route::post('/reports/generate', [ReportController::class, 'generate'])->name('admin.reports.generate');
    Route::get('/reports/download/{filename}', [ReportController::class, 'download'])->name('admin.reports.download');
    Route::get('/reports/financial', [ReportController::class, 'financial'])->name('admin.reports.financial');
    Route::get('/reports/drivers', [ReportController::class, 'driversPerformance'])->name('admin.reports.drivers');
    
    Route::get('/settings', [App\Http\Controllers\Admin\AdminSettingController::class, 'index'])->name('admin.settings.index');
    Route::post('/settings/update', [App\Http\Controllers\Admin\AdminSettingController::class, 'update'])->name('admin.settings.update');
});

/*
|--------------------------------------------------------------------------
| API ROUTES (WEB BASED)
|--------------------------------------------------------------------------
*/
Route::prefix('api')->group(function () {
    Route::get('/drivers/nearby', [DriverController::class, 'getNearbyDrivers'])->name('api.drivers.nearby');
    Route::get('/places/autocomplete', [App\Http\Controllers\Api\PlaceController::class, 'autocomplete'])->name('api.places.autocomplete');
    Route::get('/places/details', [App\Http\Controllers\Api\PlaceController::class, 'details'])->name('api.places.details');
   //API untuk mencari driver terdekat berdasarkan lokasi user
    Route::post('/drivers/find', [UserController::class, 'findDrivers'])->name('api.drivers.find');
    
    // API untuk mendapatkan lokasi driver secara real-time 
Route::get('/driver/location/{id}', [DriverController::class, 'getDriverLocation'])->name('api.driver.location');
    

    Route::middleware('auth')->group(function () {
        Route::get('/user/orders', [UserOrderController::class, 'getOrdersApi'])->name('api.user.orders');
        Route::get('/user/wallet', [WalletController::class, 'getBalance'])->name('api.user.wallet');
    });
    
    Route::middleware('auth:driver')->group(function () {
        Route::post('/driver/location', [DriverController::class, 'updateLocationApi'])->name('api.driver.location');
        Route::get('/driver/orders/new', [DriverOrderController::class, 'getNewOrders'])->name('api.driver.orders.new');
    });
});

/*
|--------------------------------------------------------------------------
| FALLBACK ROUTE (404)
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    return view('errors.404');
});