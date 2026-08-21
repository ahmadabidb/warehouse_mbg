<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class BahanBaku extends Model
{
    protected $table = 'bahan_baku';

    protected $fillable = [
        'kode_bahan', 'nama_bahan', 'kategori_id',
        'satuan', 'stok', 'stok_minimum', 'deskripsi',
    ];

    protected $casts = [
        'stok'         => 'decimal:2',
        'stok_minimum' => 'decimal:2',
    ];

    /**
     * Bahan baku milik satu kategori.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'kategori_id');
    }

    /**
     * Bahan baku memiliki banyak transaksi stok masuk.
     */
    public function stokMasuks(): HasMany
    {
        return $this->hasMany(StokMasuk::class);
    }

    /**
     * Bahan baku memiliki banyak transaksi stok keluar.
     */
    public function stokKeluars(): HasMany
    {
        return $this->hasMany(StokKeluar::class);
    }

    /**
     * Accessor untuk menampilkan status ketersediaan stok.
     * Mengembalikan: 'Habis', 'Menipis', atau 'Aman'.
     */
    public function getStatusAttribute(): string
    {
        if ($this->stok == 0) {
            return 'Habis';
        }

        return $this->stok <= $this->stok_minimum ? 'Menipis' : 'Aman';
    }
}
