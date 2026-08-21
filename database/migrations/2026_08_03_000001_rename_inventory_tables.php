<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('bahan_bakus') && ! Schema::hasTable('bahan_baku')) {
            Schema::rename('bahan_bakus', 'bahan_baku');
        }

        if (Schema::hasTable('stok_masuks') && ! Schema::hasTable('stok_masuk')) {
            Schema::rename('stok_masuks', 'stok_masuk');
        }

        if (Schema::hasTable('stok_keluars') && ! Schema::hasTable('stok_keluar')) {
            Schema::rename('stok_keluars', 'stok_keluar');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('bahan_baku') && ! Schema::hasTable('bahan_bakus')) {
            Schema::rename('bahan_baku', 'bahan_bakus');
        }

        if (Schema::hasTable('stok_masuk') && ! Schema::hasTable('stok_masuks')) {
            Schema::rename('stok_masuk', 'stok_masuks');
        }

        if (Schema::hasTable('stok_keluar') && ! Schema::hasTable('stok_keluars')) {
            Schema::rename('stok_keluar', 'stok_keluars');
        }
    }
};
