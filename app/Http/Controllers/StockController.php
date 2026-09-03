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
            'type'   => 'incoming',
            'record' => new StokMasuk,
            'items'  => BahanBaku::orderBy('nama_bahan')->get(),
        ]);
    }

    /** Menyimpan transaksi stok masuk melalui Service. */
    public function incomingStore(StockRequest $request, StockTransactionService $service)
    {
        $service->incoming($request->validated(), $request->user()->id);

        return to_route('stok-masuk.index')
            ->with('success', 'Stok masuk berhasil dicatat.');
    }

    /** Menampilkan form edit stok masuk. */
    public function incomingEdit(StokMasuk $stokMasuk)
    {
        return view('stocks.form', [
            'type'   => 'incoming',
            'record' => $stokMasuk,
            'items'  => BahanBaku::orderBy('nama_bahan')->get(),
        ]);
    }

    /** Memperbarui transaksi stok masuk melalui Service. */
    public function incomingUpdate(StockRequest $request, StokMasuk $stokMasuk, StockTransactionService $service)
    {
        $service->updateIncoming($stokMasuk, $request->validated(), $request->user()->id);

        return to_route('stok-masuk.index')
            ->with('success', 'Stok masuk berhasil diperbarui.');
    }

    /** Menghapus transaksi stok masuk melalui Service. */
    public function incomingDestroy(StokMasuk $stokMasuk, StockTransactionService $service)
    {
        $service->deleteIncoming($stokMasuk);

        return to_route('stok-masuk.index')
            ->with('success', 'Stok masuk berhasil dihapus.');
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
            'type'   => 'outgoing',
            'record' => new StokKeluar,
            'items'  => BahanBaku::orderBy('nama_bahan')->get(),
        ]);
    }

    /** Menyimpan transaksi stok keluar melalui Service. */
    public function outgoingStore(StockRequest $request, StockTransactionService $service)
    {
        $service->outgoing($request->validated(), $request->user()->id);

        return to_route('stok-keluar.index')
            ->with('success', 'Stok keluar berhasil dicatat.');
    }

    /** Menampilkan form edit stok keluar. */
    public function outgoingEdit(StokKeluar $stokKeluar)
    {
        return view('stocks.form', [
            'type'   => 'outgoing',
            'record' => $stokKeluar,
            'items'  => BahanBaku::orderBy('nama_bahan')->get(),
        ]);
    }

    /** Memperbarui transaksi stok keluar melalui Service. */
    public function outgoingUpdate(StockRequest $request, StokKeluar $stokKeluar, StockTransactionService $service)
    {
        $service->updateOutgoing($stokKeluar, $request->validated(), $request->user()->id);

        return to_route('stok-keluar.index')
            ->with('success', 'Stok keluar berhasil diperbarui.');
    }

    /** Menghapus transaksi stok keluar melalui Service. */
    public function outgoingDestroy(StokKeluar $stokKeluar, StockTransactionService $service)
    {
        $service->deleteOutgoing($stokKeluar);

        return to_route('stok-keluar.index')
            ->with('success', 'Stok keluar berhasil dihapus.');
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
