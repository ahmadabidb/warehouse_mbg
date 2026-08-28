# SIM-STOK MBG GENENGAN

**Sistem Informasi Manajemen Stok** untuk Dapur Umum (MBG Genengan) pada program **Makan Bergizi Gratis (MBG)**. Aplikasi ini digunakan untuk mengelola stok bahan baku dapur: mencatat pemasukan (stok masuk), pengeluaran (stok keluar), memantau ketersediaan stok, serta menghasilkan laporan dalam bentuk PDF dan Excel.

## Fitur Utama

- **Dashboard** dengan ringkasan statistik, grafik distribusi stok per kategori, dan tren transaksi 12 bulan (Chart.js).
- **Manajemen Bahan Baku** — kelola data bahan baku dengan kode otomatis (`BB-0001`), satuan, stok minimum, dan status stok (Aman / Menipis / Habis).
- **Manajemen Kategori** — pengelompokan bahan baku (Karbohidrat, Protein Hewani, Protein Nabati, Sayuran, Buah, Bumbu).
- **Stok Masuk (Incoming)** — catat penerimaan bahan baku dengan nomor transaksi otomatis (`SM-YYYYMMDD-XXXX`), menambah stok secara otomatis dan aman (concurrency-safe).
- **Stok Keluar (Outgoing)** — catat pengeluaran bahan baku dengan nomor transaksi otomatis (`SK-YYYYMMDD-XXXX`), memvalidasi ketersediaan stok sebelum mengurangi stok.
- **Monitoring Stok** — pantau ketersediaan stok dengan indikator status berwarna.
- **Laporan** — laporan stok masuk, stok keluar, dan stok opname dengan filter tanggal, serta ekspor **PDF** (DomPDF) dan **Excel** (Maatwebsite).
- **Manajemen Pengguna & Peran** — CRUD pengguna dan penugasan peran (role) berbasis permission (Spatie Laravel Permission).

## Teknologi

| Komponen | Teknologi |
|---|---|
| Backend | Laravel 13.8 (PHP 8.3) |
| Frontend | SB Admin 2 (Bootstrap), Tailwind CSS, Alpine.js, Chart.js, Vite |
| Database | MySQL |
| Otentikasi | Laravel Breeze (session-based) |
| Otorisasi | Spatie Laravel Permission (roles & permissions) |
| Laporan | barryvdh/laravel-dompdf, maatwebsite/excel |
| Pengujian | Pest PHP |

## Struktur Database

```
Category 1---* BahanBaku 1---* StokMasuk *---1 User
                  1---* StokKeluar *---1 User
```

| Tabel | Deskripsi |
|---|---|
| `users` | Akun pengguna sistem |
| `categories` | Kategori bahan baku |
| `bahan_baku` | Data bahan baku (kode, nama, satuan, stok, stok minimum) |
| `stok_masuk` | Transaksi pemasukan stok |
| `stok_keluar` | Transaksi pengeluaran stok |
| `permissions`, `roles`, `model_has_roles`, dll. | Tabel Spatie Permission |

## Persyaratan

- PHP >= 8.3
- Composer
- Node.js & npm
- MySQL

## Instalasi

1. **Clone repositori**
   ```bash
   git clone <url-repositori>
   cd gudang-dapur-mbg
   ```

2. **Install dependensi PHP & buat file environment**
   ```bash
   composer install
   cp .env.example .env
   ```

   > Alternatif: `composer run setup` akan otomatis mengerjakan sebagian besar langkah di atas.

3. **Konfigurasikan database** di file `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=gudang_dapur_mbg
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Generate key, migrate, dan seed**
   ```bash
   php artisan key:generate
   php artisan migrate --seed
   ```

5. **Install dependensi frontend & build**
   ```bash
   npm install
   npm run build
   ```

6. **Jalankan aplikasi**
   ```bash
   php artisan serve
   ```
   Aplikasi dapat diakses di `http://localhost:8000`.

## Akun Default (Seeder)

| Nama | Email | Peran | Password |
|---|---|---|---|
| Admin Gudang | `admingudang@mbg.test` | Admin | `password` |
| User 1 | `user1@mbg.test` | User | `password` |
| User 2 | `user2@mbg.test` | User | `password` |
| User 3 | `user3@mbg.test` | User | `password` |

## Peran & Izin (Roles & Permissions)

Sistem menggunakan **12 permission** dan **2 peran (role)**:

- **Admin** — memiliki semua izin (kelola pengguna, kategori, bahan baku, stok, laporan, dashboard).
- **User** — melihat dashboard, melihat bahan baku, mencatat stok masuk/keluar, memantau stok, serta melihat & mengekspor laporan.

Daftar permission:

| Permission | Deskripsi |
|---|---|
| `dashboard.view` | Melihat dashboard |
| `user.manage` | Kelola pengguna |
| `category.manage` | Kelola kategori |
| `bahan_baku.manage` | Kelola bahan baku |
| `bahan_baku.view` | Melihat bahan baku (hanya baca) |
| `stok_masuk.create` | Mencatat stok masuk |
| `stok_keluar.create` | Mencatat stok keluar |
| `stok.view` | Memantau stok |
| `laporan.view` | Melihat laporan |
| `laporan.export` | Mengekspor laporan (PDF/Excel) |

## Arsitektur

- **Service Layer** — logika bisnis dipisahkan ke `BahanBakuService` dan `StockTransactionService` agar controller tetap ramping.
- **Generasi Kode Otomatis** — kode bahan baku (`BB-0001`) dan nomor transaksi (`SM/SK-YYYYMMDD-XXXX`) dihasilkan otomatis.
- **Keamanan Konkurensi** — semua operasi pembuatan kode dan perubahan stok dibungkus `DB::transaction` + `lockForUpdate()` untuk mencegah race condition.
- **Integritas Stok** — pengurangan stok hanya dilakukan jika stok mencukupi, jika tidak akan memunculkan pesan error yang ramah.
- **RBAC** — kontrol akses berbasis peran; elemen UI ditampilkan/disembunyikan sesuai izin pengguna (`@can`).
- **Form Request & Policy** — validasi form pada kelas khusus serta policy untuk kontrol akses level model.

## Menjalankan Pengujian

```bash
composer test
# atau
php artisan test
```

## Lisensi

Aplikasi ini dikembangkan untuk keperluan Tugas Akhir / Skripsi Sistem Informasi Manajemen Stok Dapur Umum MBG Genengan.
