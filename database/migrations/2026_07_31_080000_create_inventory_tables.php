<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel kategori bahan baku
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kategori')->unique();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // Tabel bahan baku (master data)
        Schema::create('bahan_baku', function (Blueprint $table) {
            $table->id();
            $table->string('kode_bahan')->unique();      // contoh: BB-0001
            $table->string('nama_bahan');
            $table->foreignId('kategori_id')
                  ->constrained('categories')
                  ->restrictOnDelete();
            $table->string('satuan', 20);               // Kg, Gram, Liter, dst.
            $table->decimal('stok', 12, 2)->default(0);
            $table->decimal('stok_minimum', 12, 2)->default(0);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // Tabel transaksi stok masuk
         Schema::create('stok_masuk', function (Blueprint $table) {
             $table->id();
             $table->string('nomor_transaksi')->unique();  // contoh: SM-20260801-0001
             $table->date('tanggal_masuk');
             $table->foreignId('bahan_baku_id')
                   ->constrained('bahan_baku')
                   ->restrictOnDelete();
            $table->decimal('jumlah', 12, 2);
            $table->string('supplier')->nullable();
            $table->date('tanggal_expired')->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('user_id')
                  ->constrained()
                  ->restrictOnDelete();
            $table->timestamps();
        });

        // Tabel transaksi stok keluar
         Schema::create('stok_keluar', function (Blueprint $table) {
             $table->id();
             $table->string('nomor_transaksi')->unique();  // contoh: SK-20260801-0001
             $table->date('tanggal_keluar');
             $table->foreignId('bahan_baku_id')
                   ->constrained('bahan_baku')
                   ->restrictOnDelete();
            $table->decimal('jumlah', 12, 2);
            $table->text('keterangan')->nullable();
            $table->foreignId('user_id')
                  ->constrained()
                  ->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_keluar');
        Schema::dropIfExists('stok_masuk');
        Schema::dropIfExists('bahan_baku');
        Schema::dropIfExists('categories');
    }
};
