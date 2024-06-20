<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StockTransactionController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Stock Transactions Routes
    Route::resource('stock-transactions', StockTransactionController::class);
    Route::post('stock-transactions/import', [StockTransactionController::class, 'import'])->name('stock-transactions.import');
    Route::get('/transactions/{id}', [StockTransactionController::class, 'show'])->name('transactions.show');

    Route::get('/upload-csv', [StockTransactionController::class, 'showUploadForm'])->name('upload-csv');
    Route::post('/upload-csv', [StockTransactionController::class, 'processUpload'])->name('process-upload');

    Route::post('/stock-transactions/{stock_symbol}/upload-logo', [StockTransactionController::class, 'uploadLogo'])->name('stock-transactions.upload-logo');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
