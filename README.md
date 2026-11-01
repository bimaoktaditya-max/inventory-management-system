# Inventory Management System

Aplikasi web sederhana untuk membantu pengelolaan persediaan barang gudang, mulai dari pencatatan barang masuk dan keluar, pemantauan stok, hingga riwayat transaksi dan kartu stok bulanan.

## 1. Teknologi yang Digunakan

- **Backend:** PHP 8.5
- **Framework:** Laravel 13
- **Database:** SQLite
- **Frontend:** HTML, CSS, JavaScript, Blade Template
- **Development Environment:** Laravel Herd

## 2. Fitur Aplikasi

### Authentication
- Login dan logout pengguna.
- Halaman utama dan fitur transaksi dilindungi autentikasi.

### Master Barang
- Menambahkan data barang.
- Menyimpan kode barang, nama barang, satuan, dan minimum stok.
- Kode barang harus unik.
- Menampilkan jumlah stok terkini.
- Memberikan indikator stok aman atau stok menipis.

### Transaksi Stok
- Pencatatan barang masuk (IN).
- Pencatatan barang keluar (OUT).
- Pencatatan supplier atau tujuan barang.
- Pencatatan pengguna yang melakukan transaksi.
- Validasi agar barang keluar tidak melebihi stok tersedia.
- Pencegahan pengiriman ulang transaksi menggunakan idempotency key.
- Kategori transaksi:
  - NORMAL
  - CORRECTION
  - STOCK_OPNAME

### Stock Opname
- Membandingkan stok sistem dengan stok fisik.
- Membuat transaksi penyesuaian otomatis berdasarkan selisih.
- Tidak membuat transaksi penyesuaian jika stok fisik sama dengan stok sistem.

### Kartu Stok
- Menampilkan saldo awal.
- Menampilkan total barang masuk.
- Menampilkan total barang keluar.
- Menampilkan saldo akhir.
- Menghitung saldo awal berdasarkan saldo akhir periode sebelumnya.

## 3. Instalasi dan Konfigurasi

Pastikan perangkat sudah memiliki:
- PHP 8.2 atau lebih baru.
- Composer.
- SQLite.
- Git.

### Clone Repository

```bash
git clone <REPOSITORY_URL>
cd inventory-app
```

### Install Dependencies

```bash
composer install
```

### Konfigurasi Environment

Salin file environment:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

Atur konfigurasi database pada file `.env`:

```env
DB_CONNECTION=sqlite
```

Pastikan file database tersedia di:

```text
database/database.sqlite
```

Jika belum tersedia, buat file tersebut sebelum menjalankan migrasi.

### Jalankan Migrasi dan Seeder

```bash
php artisan migrate --seed
```

Jika akun demo belum dibuat oleh DatabaseSeeder, jalankan:

```bash
php artisan db:seed --class=UserSeeder
```

### Jalankan Aplikasi

```bash
php artisan serve
```

Buka aplikasi melalui:

```text
http://127.0.0.1:8000
```

## 4. Akun Demo

| Informasi | Nilai |
|---|---|
| Email | admin@gudang.test |
| Password | Gudang123! |
| Role | Admin Gudang |

Akun tersebut digunakan untuk keperluan pengujian aplikasi.

## 5. Menjalankan Automated Test

Jalankan perintah:

```bash
php artisan test
```

Test yang tersedia mem