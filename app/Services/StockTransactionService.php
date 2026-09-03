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

    /**
     * Memperbarui transaksi stok masuk dan mengoreksi efek transaksi lama
     * terhadap stok bahan baku sebelum menerapkan data baru.
     * Seluruh proses atomic dan aman dari race condition.
     *
     * @throws \Throwable
     */
    public function updateIncoming(StokMasuk $entry, array $data, int $userId): StokMasuk
    {
        return DB::transaction(function () use ($entry, $data, $userId) {
            $oldItem = $entry->bahanBaku()->lockForUpdate()->first();
            $newItem = BahanBaku::lockForUpdate()->findOrFail($data['bahan_baku_id']);

            if ($oldItem->id === $newItem->id) {
                // Bahan tidak berubah: cukup kurangi/tambahkan selisih jumlah pada stok yang sama
                $diff = (float) $data['jumlah'] - (float) $entry->jumlah;
                $newItem->increment('stok', $diff);
            } else {
                // Bahan berubah: kembalikan stok bahan lama, lalu tambahkan stok bahan baru
                $oldItem->decrement('stok', $entry->jumlah);
                $newItem->increment('stok', $data['jumlah']);
            }

            $data['user_id'] = $userId;
            $entry->update($data);

            return $entry->fresh();
        });
    }

    /**
     * Menghapus transaksi stok masuk dan mengembalikan stok bahan baku
     * sebesar jumlah transaksi yang dihapus.
     * Ditolak jika pembalikan menyebabkan stok bahan baku negatif.
     *
     * @throws \Throwable
     */
    public function deleteIncoming(StokMasuk $entry): void
    {
        DB::transaction(function () use ($entry) {
            // Kunci baris bahan baku untuk mencegah race condition
            $item = $entry->bahanBaku()->lockForUpdate()->first();

            // Tolak jika pembalikan membuat stok negatif
            if ($item->stok < $entry->jumlah) {
                throw ValidationException::withMessages([
                    'keterangan' => 'Tidak dapat menghapus: pembalikan transaksi akan membuat stok "' .
                                    $item->nama_bahan . '" menjadi negatif (' . $item->stok . ' ' .
                                    $item->satuan . ' tersedia, ' . $entry->jumlah . ' akan dikurangi).',
                ]);
            }

            $item->decrement('stok', $entry->jumlah);
            $entry->delete();
        });
    }

    /**
     * Memperbarui transaksi stok keluar. Terlebih dahulu mengembalikan stok
     * transaksi lama, lalu memvalidasi dan menerapkan transaksi baru.
     * Ditolak jika stok bahan baku baru tidak mencukupi.
     *
     * @throws ValidationException
     * @throws \Throwable
     */
    public function updateOutgoing(StokKeluar $entry, array $data, int $userId): StokKeluar
    {
        return DB::transaction(function () use ($entry, $data, $userId) {
            $oldJumlah        = (float) $entry->jumlah;
            $oldBahanBakuId   = $entry->bahan_baku_id;
            $newBahanBakuId   = (int) $data['bahan_baku_id'];
            $newJumlah        = (float) $data['jumlah'];

            // Kunci bahan lama dan bahan baru (kunci terlebih dahulu untuk mencegah race condition)
            $oldItem = $entry->bahanBaku()->lockForUpdate()->first();
            $newItem = BahanBaku::lockForUpdate()->findOrFail($newBahanBakuId);

            // Kembalikan efek transaksi lama terlebih dahulu
            $oldItem->increment('stok', $oldJumlah);

            // Jika bahan baku sama, $newItem adalah instans terpisah yang nilai stoknya
            // masih basi (belum termasuk pengembalian di atas). Segarkan agar validasi
            // menggunakan stok yang sudah dikembalikan.
            if ($oldItem->id === $newItem->id) {
                $newItem->refresh();
            }

            // Validasi kecukupan stok bahan baku baru setelah transaksi lama dikembalikan
            if ($newItem->stok < $newJumlah) {
                throw ValidationException::withMessages([
                    'jumlah' => 'Stok tidak mencukupi. Stok "' . $newItem->nama_bahan . '" tersedia: ' .
                                $newItem->stok . ' ' . $newItem->satuan,
                ]);
            }

            // Kurangi stok berdasarkan transaksi baru
            $newItem->decrement('stok', $newJumlah);

            $data['user_id'] = $userId;
            $entry->update($data);

            return $entry->fresh();
        });
    }

    /**
     * Menghapus transaksi stok keluar dan mengembalikan stok bahan baku
     * sebesar jumlah transaksi yang dihapus.
     *
     * @throws \Throwable
     */
    public function deleteOutgoing(StokKeluar $entry): void
    {
        DB::transaction(function () use ($entry) {
            // Kunci baris bahan baku untuk mencegah race condition
            $item = $entry->bahanBaku()->lockForUpdate()->first();

            // Kembalikan stok (karena create mengurangi stok)
            $item->increment('stok', $entry->jumlah);
            $entry->delete();
        });
    }
}
