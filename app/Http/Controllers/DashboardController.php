<?php

namespace App\Http\Controllers;

use App\Models\{BahanBaku, Category, StokMasuk, StokKeluar};

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard dengan ringkasan statistik inventaris
     * dan data grafik pergerakan stok 12 bulan terakhir.
     */
    public function __invoke()
    {
        // Buat label dan data untuk grafik 12 bulan terakhir
        $months   = collect(range(11, 0))->map(fn ($i) => now()->subMonths($i));

        $incoming = $months->map(fn ($d) => StokMasuk::whereYear('tanggal_masuk', $d->year)
                                                      ->whereMonth('tanggal_masuk', $d->month)
                                                      ->sum('jumlah'));

        $outgoing = $months->map(fn ($d) => StokKeluar::whereYear('tanggal_keluar', $d->year)
                                                       ->whereMonth('tanggal_keluar', $d->month)
                                                       ->sum('jumlah'));

        return view('dashboard', [
            // Kartu ringkasan statistik
            'totalItems'    => BahanBaku::count(),
            'totalStockItems' => BahanBaku::count(),
            'lowItems'      => BahanBaku::where('stok', '>', 0)
                                        ->whereColumn('stok', '<=', 'stok_minimum')
                                        ->count(),
            'outItems'      => BahanBaku::where('stok', 0)->count(),
            
            // Data stok per kategori dengan satuan
            'topStocks' => BahanBaku::with('category')
                                    ->orderBy('stok', 'desc')
                                    ->limit(5)
                                    ->get(),
            
            // Transaksi bulan ini
            'incomingMonth' => StokMasuk::whereMonth('tanggal_masuk', now()->month)
                                        ->whereYear('tanggal_masuk', now()->year)
                                        ->count(),
            'outgoingMonth' => StokKeluar::whereMonth('tanggal_keluar', now()->month)
                                         ->whereYear('tanggal_keluar', now()->year)
                                         ->count(),

            // Data grafik pergerakan stok
            'labels'         => $months->map(fn ($d) => $d->format('M Y')),
            'incoming'       => $incoming,
            'outgoing'       => $outgoing,

            // Data grafik stok per kategori
            'categoryLabels' => Category::pluck('nama_kategori'),
            'categoryStocks' => Category::withSum('bahanBakus', 'stok')
                                        ->get()
                                        ->pluck('bahan_bakus_sum_stok'),
        ]);
    }
}
