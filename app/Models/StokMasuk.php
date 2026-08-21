<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StokMasuk extends Model
{
    protected $table = 'stok_masuk';

    protected $fillable = [
        'nomor_transaksi', 'tanggal_masuk', 'bahan_baku_id',
        'jumlah', 'supplier', 'tanggal_expired', 'keterangan', 'user_id',
    ];

    protected $casts = [
        'tanggal_masuk'   => 'date',
        'tanggal_expired' => 'date',
        'jumlah'          => 'decimal:2',
    ];

    /** Stok masuk milik satu bahan baku. */
    public function bahanBaku(): BelongsTo
    {
        return $this->belongsTo(BahanBaku::class);
    }

    /** Stok masuk dicatat oleh satu pengguna. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
