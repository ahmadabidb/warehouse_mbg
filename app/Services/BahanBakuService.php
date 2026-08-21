<?php

namespace App\Services;

use App\Models\BahanBaku;
use Illuminate\Support\Facades\DB;

class BahanBakuService
{
    /**
     * Membuat bahan baku baru dengan kode yang digenerate otomatis.
     * Menggunakan DB::transaction dan lockForUpdate() untuk menghindari
     * race condition saat pembuatan kode secara bersamaan.
     */
    public function create(array $data): BahanBaku
    {
        return DB::transaction(function () use ($data) {
            // Ambil kode terakhir dengan lock untuk mencegah duplikasi
            $lastCode = BahanBaku::query()
                ->lockForUpdate()
                ->orderByDesc('id')
                ->value('kode_bahan');

            // Hitung nomor urut berikutnya
            $nextNumber = $lastCode ? ((int) substr($lastCode, 3)) + 1 : 1;

            // Format: BB-0001, BB-0002, dst.
            $data['kode_bahan'] = 'BB-' . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);

            return BahanBaku::create($data);
        });
    }
}
