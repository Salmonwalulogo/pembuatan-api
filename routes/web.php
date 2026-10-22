<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StrukController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->controller(AdminController::class)->group(function () {
    Route::get('/admin/dashboard', 'dashboard')->name('admin.dashboard');
    Route::get('/admin/users', 'users')->name('admin.users');
    Route::get('/admin/data', 'data')->name('admin.data');
    Route::get('/admin/settings', 'settings')->name('admin.settings');
});


Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('products', ProductController::class);
});

Route::middleware(['auth', 'kasir'])->group(function () {
    Route::get('/kasir', [CashierController::class, 'index'])->name('cashier.index');
    Route::post('/kasir/transaksi', [CashierController::class, 'store'])->name('cashier.store');
    Route::post('/kasir/struk', [StrukController::class, 'struk'])->name('cashier.struk');
});

require __DIR__.'/auth.php';
