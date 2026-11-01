<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\StockTransactionController;
use App\Http\Controllers\StockCardController;

// Halaman untuk pengguna yang belum login
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.process');
});

// Halaman untuk pengguna yang sudah login
Route::middleware('auth')->group(function () {

    Route::get('/', function () {
        return redirect()->route('items.index');
    });

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    // Master Barang
    Route::get('/items', [ItemController::class, 'index'])
        ->name('items.index');

    Route::post('/items', [ItemController::class, 'store'])
        ->name('items.store');

    // Transaksi Stok
    Route::get('/transactions', [StockTransactionController::class, 'index'])
        ->name('transactions.index');

    Route::post('/transactions', [StockTransactionController::class, 'store'])
        ->name('transactions.store');

    // Kartu Stok Bulanan
    Route::get('/stock-cards', [StockCardController::class, 'index'])
        ->name('stock-cards.index');
});