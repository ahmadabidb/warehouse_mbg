<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StokKeluar extends Model
{
    protected $table = 'stok_keluar';

    protected $fillable = [
        'nomor_transaksi', 'tanggal_keluar', 'bahan_baku_id',
        'jumlah', 'keterangan', 'user_id',
    ];

    protected $casts = [
        'tanggal_keluar' => 'date',
        'jumlah'         => 'decimal:2',
    ];

    /** Stok keluar milik satu bahan baku. */
    public function bahanBaku(): BelongsTo
    {
        return $this->belongsTo(BahanBaku::class);
    }

    /** Stok keluar dicatat oleh satu pengguna. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
