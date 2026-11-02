# Inventory Management System

Aplikasi web sederhana untuk membantu pengelolaan persediaan barang gudang, mulai dari pencatatan barang masuk dan keluar, pemantauan stok, stock opname, hingga riwayat transaksi dan kartu stok bulanan.

## 1. Teknologi yang Digunakan

- **Backend:** PHP 8.5
- **Framework:** Laravel 13
- **Database:** SQLite
- **Frontend:** HTML, CSS, JavaScript, Blade Template
- **Development Environment:** Laravel Herd

## 2. Fitur Aplikasi

### Authentication
- Login dan logout pengguna.
- Halaman utama dan fitur aplikasi dilindungi autentikasi.
- Pencatatan pengguna yang melakukan transaksi.

### Master Barang
- Menambahkan data barang.
- Mengedit informasi barang.
- Menghapus barang yang belum memiliki riwayat transaksi.
- Menyimpan kode barang, nama barang, satuan, dan minimum stok.
- Validasi kode barang agar unik.
- Kode barang tidak dapat diubah jika sudah memiliki riwayat transaksi.
- Menampilkan jumlah stok terkini.
- Indikator stok aman atau stok menipis.

### Transaksi Stok
- Pencatatan barang masuk (IN).
- Pencatatan barang keluar (OUT).
- Pencatatan supplier atau tujuan barang melalui informasi referensi.
- Pencatatan pengguna yang melakukan transaksi.
- Validasi agar barang keluar tidak melebihi stok tersedia.
- Pencegahan transaksi ganda melalui idempotency key.
- Transaksi yang sudah tersimpan tidak dapat diedit atau dihapus melalui aplikasi.
- Kategori transaksi:
  - NORMAL
  - CORRECTION
  - STOCK_OPNAME

### Stock Opname
- Membandingkan stok sistem dengan stok fisik.
- Membuat transaksi penyesuaian otomatis berdasarkan selisih.
- Tidak membuat transaksi penyesuaian jika stok fisik sama dengan stok sistem.
- Penyesuaian dicatat sebagai transaksi baru.

### Kartu Stok
- Menampilkan saldo awal.
- Menampilkan total barang masuk.
- Menampilkan total barang keluar.
- Menampilkan saldo akhir.
- Menghitung saldo awal berdasarkan saldo akhir periode sebelumnya.
- Menampilkan riwayat pergerakan stok.

## 3. Instalasi dan Konfigurasi

### Persyaratan Sistem

Pastikan perangkat telah memiliki:

- PHP 8.3 atau lebih baru.
- Composer.
- Git.
- SQLite dan ekstensi PHP SQLite (PDO SQLite).

### Clone Repository

```bash
git clone https://github.com/bimaoktaditya-max/inventory-management-system.git
cd inventory-management-system
```

### Install Dependencies

```bash
composer install
```

### Konfigurasi Environment

Salin file environment.

Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

Linux/macOS:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

Pastikan konfigurasi database pada file `.env` menggunakan SQLite:

```env
DB_CONNECTION=sqlite
```

Konfigurasi session, queue, dan cache menggunakan nilai bawaan dari `.env.example`.

### Membuat Database

Pastikan file `database/database.sqlite` tersedia.

Jika belum tersedia:

Windows PowerShell:

```powershell
New-Item database\database.sqlite -ItemType File
```

Linux/macOS:

```bash
touch database/database.sqlite
```

### Jalankan Migrasi dan Seeder

```bash
php artisan migrate --seed
```

Perintah tersebut akan membuat tabel database dan akun admin melalui DatabaseSeeder.

### Jalankan Aplikasi

```bash
php artisan serve
```

Buka aplikasi melalui browser:

http://127.0.0.1:8000

## 4. Akun Demo

| Informasi | Nilai |
|---|---|
| Email | admin@gudang.test |
| Password | Gudang123! |
| Pengguna | Admin Gudang |

Akun demo digunakan untuk keperluan pengujian aplikasi.

## 5. Menjalankan Automated Test

Jalankan perintah:

```bash
php artisan test
```

Hasil pengujian terakhir:

- 2 tests passed.
- 3 assertions passed.

Test yang tersedia mencakup pengujian unit dasar dan validasi bahwa pengguna yang belum login diarahkan ke halaman login.

Pengujian manual juga dilakukan terhadap fitur master barang, transaksi masuk dan keluar, validasi stok, stock opname, kartu stok, serta autentikasi.

## 6. Asumsi dan Batasan

- Aplikasi menggunakan SQLite untuk kebutuhan pengembangan dan pengujian.
- Akun demo digunakan sebagai pengguna administrator gudang.
- Stok dihitung berdasarkan akumulasi transaksi barang masuk dikurangi transaksi barang keluar.
- Barang yang sudah memiliki riwayat transaksi tidak dapat dihapus.
- Kode barang yang sudah memiliki riwayat transaksi tidak dapat diubah.
- Transaksi yang sudah tercatat tidak disediakan fitur edit atau hapus. Koreksi dilakukan melalui transaksi baru.
- Idempotency key digunakan untuk mencegah penyimpanan transaksi ganda akibat pengiriman ulang form.
- `lockForUpdate()` digunakan dalam proses transaksi, tetapi SQLite tidak menyediakan mekanisme row-level locking seperti database server tertentu. Karena itu, pengujian ini belum membuktikan keamanan transaksi konkuren untuk lingkungan produksi.
- Fitur Excel Export belum diimplementasikan.

## 7. Dokumentasi Pengguna

Panduan penggunaan aplikasi tersedia pada:

`docs