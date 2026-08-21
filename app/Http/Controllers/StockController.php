<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockRequest;
use App\Models\{BahanBaku, StokMasuk, StokKeluar};
use App\Services\StockTransactionService;
use Illuminate\Http\Request;

class StockController extends Controller
{
    // ─── Stok Masuk ────────────────────────────────────────────────────

    /** Menampilkan daftar transaksi stok masuk. */
    public function incomingIndex()
    {
        return view('stocks.index', [
            'type'    => 'incoming',
            'records' => StokMasuk::with('bahanBaku', 'user')
                                  ->latest('tanggal_masuk')
                                  ->paginate(10),
        ]);
    }

    /** Menampilkan form pencatatan stok masuk. */
    public function incomingCreate()
    {
        return view('stocks.form', [
            'type'  => 'incoming',
            'items' => BahanBaku::orderBy('nama_bahan')->get(),
        ]);
    }

    /** Menyimpan transaksi stok masuk melalui Service. */
    public function incomingStore(StockRequest $request, StockTransactionService $service)
    {
        $service->incoming($request->validated(), $request->user()->id);

        return to_route('stok-masuk.index')
            ->with('success', 'Stok masuk berhasil dicatat.');
    }

    // ─── Stok Keluar ───────────────────────────────────────────────────

    /** Menampilkan daftar transaksi stok keluar. */
    public function outgoingIndex()
    {
        return view('stocks.index', [
            'type'    => 'outgoing',
            'records' => StokKeluar::with('bahanBaku', 'user')
                                   ->latest('tanggal_keluar')
                                   ->paginate(10),
        ]);
    }

    /** Menampilkan form pencatatan stok keluar. */
    public function outgoingCreate()
    {
        return view('stocks.form', [
            'type'  => 'outgoing',
            'items' => BahanBaku::orderBy('nama_bahan')->get(),
        ]);
    }

    /** Menyimpan transaksi stok keluar melalui Service. */
    public function outgoingStore(StockRequest $request, StockTransactionService $service)
    {
        $service->outgoing($request->validated(), $request->user()->id);

        return to_route('stok-keluar.index')
            ->with('success', 'Stok keluar berhasil dicatat.');
    }

    // ─── Monitoring ────────────────────────────────────────────────────

    /** Menampilkan halaman monitoring stok semua bahan baku. */
    public function monitoring()
    {
        return view('stocks.monitoring', [
            'items' => BahanBaku::with('category')->paginate(15),
        ]);
    }
}
