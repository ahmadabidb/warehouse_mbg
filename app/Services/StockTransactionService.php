<?php

namespace App\Services;

use App\Models\{BahanBaku, StokMasuk, StokKeluar};
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockTransactionService
{
    /**
     * Membuat nomor transaksi secara otomatis dengan format:
     * [PREFIX]-[YYYYMMDD]-[XXXX]
     * Contoh: SM-20260801-0001, SK-20260801-0003
     */
    private function generateNumber(string $prefix, string $table, string $date): string
    {
        $base = $prefix . '-' . date('Ymd', strtotime($date));

        // Ambil nomor transaksi terakhir pada tanggal yang sama (dengan lock)
        $last = DB::table($table)
            ->where('nomor_transaksi', 'like', $base . '-%')
            ->lockForUpdate()
            ->max('nomor_transaksi');

        // Ambil 4 digit terakhir, tambah 1, lalu pad dengan nol
        $nextSeq = (int) substr($last ?? '', -4) + 1;

        return $base . '-' . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Mencatat transaksi stok masuk dan menambah jumlah stok bahan baku.
     *
     * @throws \Throwable
     */
    public function incoming(array $data, int $userId): StokMasuk
    {
        return DB::transaction(function () use ($data, $userId) {
            // Kunci baris bahan baku untuk mencegah race condition
            $item = BahanBaku::lockForUpdate()->findOrFail($data['bahan_baku_id']);

            $data['nomor_transaksi'] = $this->generateNumber('SM', 'stok_masuk', $data['tanggal_masuk']);
            $data['user_id']         = $userId;

            $entry = StokMasuk::create($data);

            // Tambah stok bahan baku secara atomik
            $item->increment('stok', $data['jumlah']);

            return $entry;
        });
    }

    /**
     * Mencatat transaksi stok keluar dan mengurangi jumlah stok bahan baku.
     * Melempar ValidationException jika stok tidak mencukupi.
     *
     * @throws ValidationException
     * @throws \Throwable
     */
    public function outgoing(array $data, int $userId): StokKeluar
    {
        return DB::transaction(function () use ($data, $userId) {
            // Kunci baris bahan baku untuk mencegah race condition
            $item = BahanBaku::lockForUpdate()->findOrFail($data['bahan_baku_id']);

            // Validasi kecukupan stok sebelum mencatat transaksi
            if ($item->stok < $data['jumlah']) {
                throw ValidationException::withMessages([
                    'jumlah' => 'Stok tidak mencukupi. Stok tersedia: ' . $item->stok . ' ' . $item->satuan,
                ]);
            }

            $data['nomor_transaksi'] = $this->generateNumber('SK', 'stok_keluar', $data['tanggal_keluar']);
            $data['user_id']         = $userId;

            $entry = StokKeluar::create($data);

            // Kurangi stok bahan baku secara atomik
            $item->decrement('stok', $data['jumlah']);

            return $entry;
        });
    }
}
