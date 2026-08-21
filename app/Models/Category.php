<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['nama_kategori', 'deskripsi'];

    /**
     * Satu kategori memiliki banyak bahan baku.
     */
    public function bahanBakus(): HasMany
    {
        return $this->hasMany(BahanBaku::class, 'kategori_id');
    }
}
