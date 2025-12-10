<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\WebhomeController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\AdminDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('home');
});

Route::get('/about', function () {
    return view('about');
});

Route::get('/index', function () {
    return view('home');
});

Route::get('/privacy-policy', [AboutController::class, 'show']);

Route::get('/deletion', [AboutController::class, 'show']);

// Admin Login Routes
Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/admin/login', [AdminLoginController::class, 'login'])->name('admin.login.submit');
});

Route::middleware('auth')->group(function () {
    Route::post('/admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');
});

// Admin Dashboard (protected by auth middleware and admin check)
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/admin/landlords', [AdminDashboardController::class, 'landlords'])->name('admin.landlords');
    Route::get('/admin/students', [AdminDashboardController::class, 'students'])->name('admin.students');
    Route::get('/admin/chart-data', [AdminDashboardController::class, 'getChartData'])->name('admin.chartData');
    Route::get('/admin/reg-payments', [AdminDashboardController::class, 'regPayments'])->name('admin.regPayments');
    Route::get('/admin/reg-payment-chart-data', [AdminDashboardController::class, 'getRegPaymentChartData'])->name('admin.regPaymentChartData');
    Route::get('/admin/direction-payments', [AdminDashboardController::class, 'directionPayments'])->name('admin.directionPayments');
    Route::get('/admin/direction-payment-chart-data', [AdminDashboardController::class, 'getDirectionPaymentChartData'])->name('admin.directionPaymentChartData');
});

require __DIR__.'/auth.php';
