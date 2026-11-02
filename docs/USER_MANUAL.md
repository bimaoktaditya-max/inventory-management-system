# USER MANUAL
## Aplikasi Inventory Management System

### 1. Pendahuluan

Inventory Management System merupakan aplikasi berbasis web untuk membantu pengelolaan persediaan barang di gudang. Aplikasi ini digunakan untuk mencatat data barang, transaksi barang masuk dan keluar, penyesuaian stok, serta memantau riwayat dan saldo persediaan.

### 2. Login Aplikasi

1. Buka aplikasi melalui browser.
2. Masukkan email dan password akun yang telah disediakan.
3. Klik tombol **Login**.
4. Setelah berhasil, pengguna akan diarahkan ke halaman utama aplikasi.

**Akun Demo:**
- Email: `admin@gudang.test`
- Password: `Gudang123!`
- Nama pengguna: Admin Gudang

### 3. Master Barang

Menu Master Barang digunakan untuk mengelola data barang yang tersedia di gudang.

Data barang meliputi:
- Kode barang
- Nama barang
- Satuan
- Minimum stok

**Menambahkan barang:**
1. Buka menu **Master Barang**.
2. Isi kode barang, nama barang, satuan, dan minimum stok.
3. Klik tombol simpan.
4. Data barang akan ditampilkan pada tabel Master Barang.

**Mengedit barang:**
1. Pilih barang pada tabel Master Barang.
2. Klik tombol **Edit**.
3. Ubah informasi barang yang diperlukan.
4. Simpan perubahan.

**Menghapus barang:**
1. Pilih barang yang akan dihapus.
2. Klik tombol **Hapus**.
3. Konfirmasi penghapusan jika diminta.

**Ketentuan Master Barang:**
- Kode barang harus unik dan tidak boleh sama dengan barang lainnya.
- Barang yang sudah memiliki riwayat transaksi tidak dapat dihapus.
- Kode barang yang sudah memiliki riwayat transaksi tidak dapat diubah.
- Informasi barang lainnya tetap dapat diperbarui selama tidak melanggar validasi sistem.

### 4. Transaksi Barang Masuk dan Keluar

Menu Transaksi digunakan untuk mencatat pergerakan stok barang.

**Barang Masuk (IN)**
1. Pilih barang.
2. Pilih jenis transaksi **IN**.
3. Masukkan tanggal dan jumlah barang.
4. Isi informasi referensi atau catatan jika diperlukan.
5. Klik tombol simpan.

**Barang Keluar (OUT)**
1. Pilih barang.
2. Pilih jenis transaksi **OUT**.
3. Masukkan tanggal dan jumlah barang.
4. Isi informasi referensi atau catatan jika diperlukan.
5. Klik tombol simpan.

Sistem akan menolak transaksi barang keluar apabila jumlah yang diminta melebihi stok tersedia. Dengan demikian, stok tidak akan menjadi negatif.

### 5. Kategori Transaksi

Aplikasi menyediakan tiga kategori transaksi:

- **NORMAL:** transaksi barang masuk atau keluar seperti biasa.
- **CORRECTION:** transaksi koreksi untuk memperbaiki pencatatan stok. Koreksi dilakukan melalui transaksi baru.
- **STOCK_OPNAME:** penyesuaian stok berdasarkan hasil perhitungan fisik barang.

### 6. Stock Opname

Stock Opname digunakan untuk menyesuaikan stok sistem dengan jumlah fisik barang di gudang.

Langkah penggunaan:
1. Buka menu Transaksi.
2. Pilih barang yang akan dihitung.
3. Pilih kategori **STOCK_OPNAME**.
4. Masukkan tanggal transaksi.
5. Masukkan jumlah stok fisik hasil perhitungan.
6. Klik tombol simpan.

Sistem akan membandingkan stok fisik dengan stok sistem. Jika terdapat selisih, sistem akan mencatat transaksi penyesuaian. Jika tidak terdapat selisih, sistem tidak membuat transaksi baru.

### 7. Riwayat Transaksi

Riwayat transaksi menampilkan catatan pergerakan stok barang, meliputi:
- Tanggal transaksi
- Kode dan nama barang
- Jenis transaksi
- Kategori transaksi
- Jumlah barang
- Referensi dan catatan
- Pengguna yang mencatat transaksi

Transaksi yang sudah tersimpan tidak disediakan fitur edit atau hapus melalui aplikasi. Koreksi dilakukan dengan membuat transaksi baru agar riwayat tetap terjaga.

Sistem juga menggunakan mekanisme idempotency untuk mencegah satu permintaan penyimpanan transaksi tercatat lebih dari satu kali.

### 8. Kartu Stok Bulanan

Menu Kartu Stok digunakan untuk melihat pergerakan persediaan berdasarkan periode bulanan.

Informasi yang ditampilkan:
- Saldo awal
- Total barang masuk
- Total barang keluar
- Saldo akhir
- Riwayat pergerakan barang pada periode tersebut

Saldo awal bulan dihitung dari saldo akhir periode sebelumnya.

Perhitungan saldo akhir:

**Saldo Akhir = Saldo Awal + Barang Masuk - Barang Keluar**

### 9. Logout

Untuk mengakhiri sesi penggunaan aplikasi:
1. Klik tombol **Logout**.
2. Sistem akan mengakhiri sesi pengguna.
3. Pengguna akan diarahkan kembali ke halaman Login.

### 10. Catatan Penggunaan

- Pastikan data barang sudah tersedia sebelum mencatat transaksi.
- Periksa jumlah stok sebelum mencatat barang keluar.
- Gunakan kategori transaksi sesuai kebutuhan.
- Periksa kembali tanggal dan jumlah sebelum menyimpan transaksi.
- Jaga kerahasiaan akun login.
- Lakukan Stock Opname secara berkala untuk memastikan kesesuaian stok sistem dengan kondisi fisik gudang.