<?php

use App\Models\BahanBaku;
use App\Models\Category;
use App\Models\StokKeluar;
use App\Models\StokMasuk;
use App\Models\User;
use App\Services\StockTransactionService;
use Illuminate\Validation\ValidationException;

function makeBahan(string $nama, float $stok): BahanBaku
{
    $category = Category::firstOrCreate(['nama_kategori' => 'Uji Kategori']);

    return BahanBaku::create([
        'kode_bahan'    => 'BB-' . str_pad((string) BahanBaku::count(), 4, '0', STR_PAD_LEFT),
        'nama_bahan'    => $nama,
        'kategori_id'   => $category->id,
        'satuan'        => 'Kg',
        'stok'          => $stok,
        'stok_minimum'  => 1,
    ]);
}

function makeUser(): User
{
    return User::create([
        'name'     => 'Petugas',
        'email'    => fake()->unique()->safeEmail(),
        'password' => bcrypt('password'),
    ]);
}

/** Ambil nilai stok sebagai float (decimal disimpan sebagai string oleh driver). */
function stok(BahanBaku $item): float
{
    return (float) $item->fresh()->stok;
}

beforeEach(function () {
    $this->service = app(StockTransactionService::class);
    $this->user    = makeUser();
});

// ─── Stok Masuk ───────────────────────────────────────────────────────────

it('membuat stok masuk menambah stok bahan baku', function () {
    $item = makeBahan('Beras', 0);

    $this->service->incoming([
        'tanggal_masuk' => '2026-08-01',
        'bahan_baku_id' => $item->id,
        'jumlah'        => 10,
    ], $this->user->id);

    expect(stok($item))->toBe(10.0);
});

it('edit stok masuk dari 10 menjadi 15 menambah selisih stok', function () {
    $item = makeBahan('Beras', 0);
    $entry = $this->service->incoming([
        'tanggal_masuk' => '2026-08-01',
        'bahan_baku_id' => $item->id,
        'jumlah'        => 10,
    ], $this->user->id);

    $this->service->updateIncoming($entry, [
        'tanggal_masuk' => '2026-08-01',
        'bahan_baku_id' => $item->id,
        'jumlah'        => 15,
    ], $this->user->id);

    expect(stok($item))->toBe(15.0);
    expect((float) $entry->fresh()->jumlah)->toBe(15.0);
});

it('edit stok masuk dari 15 menjadi 5 mengurangi selisih stok', function () {
    $item = makeBahan('Beras', 0);
    $entry = $this->service->incoming([
        'tanggal_masuk' => '2026-08-01',
        'bahan_baku_id' => $item->id,
        'jumlah'        => 15,
    ], $this->user->id);

    $this->service->updateIncoming($entry, [
        'tanggal_masuk' => '2026-08-01',
        'bahan_baku_id' => $item->id,
        'jumlah'        => 5,
    ], $this->user->id);

    expect(stok($item))->toBe(5.0);
});

it('edit stok masuk mengganti bahan baku mengoreksi stok keduanya', function () {
    $old = makeBahan('Beras', 0);
    $new = makeBahan('Minyak', 0);
    $entry = $this->service->incoming([
        'tanggal_masuk' => '2026-08-01',
        'bahan_baku_id' => $old->id,
        'jumlah'        => 10,
    ], $this->user->id);
    // Tambahkan stok lain pada bahan baru sebagai baseline
    $new->increment('stok', 50);

    $this->service->updateIncoming($entry, [
        'tanggal_masuk' => '2026-08-01',
        'bahan_baku_id' => $new->id,
        'jumlah'        => 20,
    ], $this->user->id);

    expect(stok($old))->toBe(0.0);   // 10 dikembalikan (dikurangi)
    expect(stok($new))->toBe(70.0);  // 50 + 20
    expect($entry->fresh()->bahan_baku_id)->toBe($new->id);
});

it('hapus stok masuk mengurangi stok bahan baku', function () {
    $item = makeBahan('Beras', 0);
    $entry = $this->service->incoming([
        'tanggal_masuk' => '2026-08-01',
        'bahan_baku_id' => $item->id,
        'jumlah'        => 10,
    ], $this->user->id);

    $this->service->deleteIncoming($entry);

    expect(stok($item))->toBe(0.0);
    expect(StokMasuk::find($entry->id))->toBeNull();
});

it('menolak hapus stok masuk yang membuat stok negatif', function () {
    $item = makeBahan('Beras', 10);
    $entry = $this->service->incoming([
        'tanggal_masuk' => '2026-08-01',
        'bahan_baku_id' => $item->id,
        'jumlah'        => 10,
    ], $this->user->id);
    // Setelah transaksi, stok = 20. Turunkan stok dengan cara lain hingga
    // kurang dari jumlah transaksi agar pembalikan membuat stok negatif.
    $item->decrement('stok', 15); // stok sekarang 5 < 10

    $this->service->deleteIncoming($entry);
})->throws(ValidationException::class);

// ─── Stok Keluar ──────────────────────────────────────────────────────────

it('membuat stok keluar mengurangi stok bahan baku', function () {
    $item = makeBahan('Beras', 20);

    $this->service->outgoing([
        'tanggal_keluar' => '2026-08-01',
        'bahan_baku_id'  => $item->id,
        'jumlah'         => 5,
    ], $this->user->id);

    expect(stok($item))->toBe(15.0);
});

it('edit stok keluar dari 5 menjadi 8 mengoreksi stok', function () {
    $item = makeBahan('Beras', 20);
    $entry = $this->service->outgoing([
        'tanggal_keluar' => '2026-08-01',
        'bahan_baku_id'  => $item->id,
        'jumlah'         => 5,
    ], $this->user->id);

    $this->service->updateOutgoing($entry, [
        'tanggal_keluar' => '2026-08-01',
        'bahan_baku_id'  => $item->id,
        'jumlah'         => 8,
    ], $this->user->id);

    expect(stok($item))->toBe(12.0); // 20 - 8
    expect((float) $entry->fresh()->jumlah)->toBe(8.0);
});

it('menolak edit stok keluar melebihi stok tersedia', function () {
    $item = makeBahan('Beras', 5);
    $entry = $this->service->outgoing([
        'tanggal_keluar' => '2026-08-01',
        'bahan_baku_id'  => $item->id,
        'jumlah'         => 5,
    ], $this->user->id);
    // stok sekarang 0

    $this->service->updateOutgoing($entry, [
        'tanggal_keluar' => '2026-08-01',
        'bahan_baku_id'  => $item->id,
        'jumlah'         => 10,
    ], $this->user->id);
})->throws(ValidationException::class);

it('edit stok keluar mengganti bahan baku mengembalikan bahan lama lalu kurangi bahan baru', function () {
    $old = makeBahan('Beras', 10);
    $new = makeBahan('Minyak', 50);
    $entry = $this->service->outgoing([
        'tanggal_keluar' => '2026-08-01',
        'bahan_baku_id'  => $old->id,
        'jumlah'         => 10,
    ], $this->user->id);

    $this->service->updateOutgoing($entry, [
        'tanggal_keluar' => '2026-08-01',
        'bahan_baku_id'  => $new->id,
        'jumlah'         => 8,
    ], $this->user->id);

    expect(stok($old))->toBe(10.0);  // dikembalikan penuh
    expect(stok($new))->toBe(42.0);  // 50 - 8
    expect($entry->fresh()->bahan_baku_id)->toBe($new->id);
});

it('hapus stok keluar mengembalikan stok bahan baku', function () {
    $item = makeBahan('Beras', 20);
    $entry = $this->service->outgoing([
        'tanggal_keluar' => '2026-08-01',
        'bahan_baku_id'  => $item->id,
        'jumlah'         => 5,
    ], $this->user->id);

    expect(stok($item))->toBe(15.0);

    $this->service->deleteOutgoing($entry);

    expect(stok($item))->toBe(20.0);
    expect(StokKeluar::find($entry->id))->toBeNull();
});

// ─── Bug Fix: validasi edit stok keluar harus dihitung setelah efek transaksi lama dikembalikan ───

it('edit stok keluar 50 menjadi 55 diperbolehkan dan stok akhir 5 (bahan sama)', function () {
    // Stok awal 60, keluar 50 -> stok 10
    $item = makeBahan('Beras', 60);
    $entry = $this->service->outgoing([
        'tanggal_keluar' => '2026-08-01',
        'bahan_baku_id'  => $item->id,
        'jumlah'         => 50,
    ], $this->user->id);
    expect(stok($item))->toBe(10.0);

    // Edit menjadi 55: available = 10 + 50 = 60, 55 <= 60 -> berhasil, stok akhir 5
    $this->service->updateOutgoing($entry, [
        'tanggal_keluar' => '2026-08-01',
        'bahan_baku_id'  => $item->id,
        'jumlah'         => 55,
    ], $this->user->id);

    expect(stok($item))->toBe(5.0);
    expect((float) $entry->fresh()->jumlah)->toBe(55.0);
});

it('edit stok keluar 50 menjadi 60 berhasil dengan stok akhir 0', function () {
    $item = makeBahan('Beras', 60);
    $entry = $this->service->outgoing([
        'tanggal_keluar' => '2026-08-01',
        'bahan_baku_id'  => $item->id,
        'jumlah'         => 50,
    ], $this->user->id);
    expect(stok($item))->toBe(10.0);

    // available = 10 + 50 = 60, 60 <= 60 -> berhasil, stok akhir 0
    $this->service->updateOutgoing($entry, [
        'tanggal_keluar' => '2026-08-01',
        'bahan_baku_id'  => $item->id,
        'jumlah'         => 60,
    ], $this->user->id);

    expect(stok($item))->toBe(0.0);
});

it('edit stok keluar 50 menjadi 61 ditolak (melebihi stok yang dikembalikan)', function () {
    $item = makeBahan('Beras', 60);
    $entry = $this->service->outgoing([
        'tanggal_keluar' => '2026-08-01',
        'bahan_baku_id'  => $item->id,
        'jumlah'         => 50,
    ], $this->user->id);
    expect(stok($item))->toBe(10.0);

    // available = 10 + 50 = 60, 61 > 60 -> ditolak
    $this->service->updateOutgoing($entry, [
        'tanggal_keluar' => '2026-08-01',
        'bahan_baku_id'  => $item->id,
        'jumlah'         => 61,
    ], $this->user->id);
})->throws(ValidationException::class);
