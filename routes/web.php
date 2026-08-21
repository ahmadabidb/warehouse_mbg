<?php

use App\Http\Controllers\{ProfileController,DashboardController,CategoryController,BahanBakuController,StockController,UserController,ReportController};
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::get('/dashboard', DashboardController::class)->middleware(['auth','permission:dashboard.view'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('categories',CategoryController::class)->except('show');
    Route::resource('bahan-bakus',BahanBakuController::class)->except('show');
    Route::get('stok-masuk',[StockController::class,'incomingIndex'])->middleware('permission:stok_masuk.create')->name('stok-masuk.index'); Route::get('stok-masuk/create',[StockController::class,'incomingCreate'])->middleware('permission:stok_masuk.create')->name('stok-masuk.create');Route::post('stok-masuk',[StockController::class,'incomingStore'])->middleware('permission:stok_masuk.create')->name('stok-masuk.store');
    Route::get('stok-keluar',[StockController::class,'outgoingIndex'])->middleware('permission:stok_keluar.create')->name('stok-keluar.index'); Route::get('stok-keluar/create',[StockController::class,'outgoingCreate'])->middleware('permission:stok_keluar.create')->name('stok-keluar.create');Route::post('stok-keluar',[StockController::class,'outgoingStore'])->middleware('permission:stok_keluar.create')->name('stok-keluar.store');
    Route::get('stok-monitoring',[StockController::class,'monitoring'])->middleware('permission:stok.view')->name('stok.monitoring');
    Route::get('reports/incoming',[ReportController::class,'incoming'])->middleware('permission:laporan.view')->name('reports.incoming');Route::get('reports/outgoing',[ReportController::class,'outgoing'])->middleware('permission:laporan.view')->name('reports.outgoing');Route::get('reports/opname',[ReportController::class,'opname'])->middleware('permission:laporan.view')->name('reports.opname');Route::get('reports/{type}/export/{format}',[ReportController::class,'export'])->middleware('permission:laporan.export')->whereIn('type',['incoming','outgoing','opname'])->whereIn('format',['pdf','excel'])->name('reports.export');
    Route::resource('users',UserController::class)->except('show');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
