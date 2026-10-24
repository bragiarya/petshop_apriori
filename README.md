# Petshop Apriori

Aplikasi manajemen petshop berbasis web untuk mengelola produk, stok, dan transaksi penjualan. Data transaksi dapat dianalisis menggunakan algoritma **Apriori** untuk menemukan frequent itemset dan aturan asosiasi sebagai bahan rekomendasi bundling atau penempatan produk.

## Fitur

- Dashboard ringkasan data petshop.
- Kelola produk dan stok masuk/keluar.
- Kasir: buat, ubah, lihat riwayat, dan hapus transaksi.
- Import transaksi historis dari CSV.
- Analisis Apriori dengan parameter minimum support dan minimum confidence.
- Export aturan asosiasi ke Excel (`.xlsx`).

## Teknologi

- PHP 8.2 atau lebih baru
- CodeIgniter 4.7
- MySQL/MariaDB
- PhpSpreadsheet 5
- Composer

## Persiapan lokal

1. Pastikan PHP 8.2+, Composer, dan MySQL/MariaDB tersedia. Untuk XAMPP, aktifkan Apache dan MySQL.
2. Instal dependensi:

   ```bash
   composer install
   ```

3. Buat database kosong, misalnya `petshop_apriori`.
4. Buat file `.env`, lalu tambahkan dan sesuaikan nilai berikut:

   ```ini
   CI_ENVIRONMENT = development
   app.baseURL = 'http://localhost/petshop-apriori/public/'

   database.default.hostname = localhost
   database.default.database = petshop_apriori
   database.default.username = root
   database.default.password =
   database.default.DBDriver = MySQLi
   database.default.port = 3306
   ```

   Jangan commit `.env`; file ini dapat berisi kredensial lokal dan memang sudah diabaikan Git.

5. Jalankan migrasi untuk membuat tabel aplikasi:

   ```bash
   php spark migrate
   ```

6. Opsional, isi database baru dengan dataset demo (151 transaksi dan katalog produk):

   ```bash
   php spark db:seed RealDataSeeder
   ```

7. Jalankan aplikasi:

   ```bash
   php spark serve
   ```

   Lalu buka alamat yang ditampilkan terminal, umumnya `http://localhost:8080`. Jika memakai Apache XAMPP tanpa virtual host, akses `http://localhost/petshop-apriori/public/`.

## Format import CSV

Format kolom yang diterima aplikasi adalah:

```text
Tanggal,ID_Transaksi,Item
01/01/2026,T001,Makanan Kucing,Pasir Kucing
```

Gunakan menu **Transaksi → Riwayat Transaksi → Import**. Nama produk dalam CSV harus sudah terdaftar di database; gunakan `RealDataSeeder` atau tambahkan produk terlebih dahulu. Opsi pengosongan data lama akan menghapus riwayat transaksi sebelum proses import, jadi gunakan dengan hati-hati.

## Analisis Apriori

Buka menu **Analisis Apriori**, tentukan minimum support dan minimum confidence (nilai 0–1). Sistem menghitung support setiap item, frequent itemset, dan association rules beserta confidence serta lift. Nilai bawaan adalah support `0.2` (20%) dan confidence `0.5` (50%).

## Struktur penting

```text
app/
├── Controllers/          # Logika dashboard, produk, stok, transaksi, dan Apriori
├── Database/
│   ├── Migrations/       # Skema database
│   └── Seeds/            # Data awal/demo
├── Libraries/AprioriAlgorithm.php
├── Models/
└── Views/
public/                   # Document root web server
```

## Pengujian

```bash
composer test
```

## Kontribusi dan Git

Sebelum membuat commit, jangan sertakan `.env`, `vendor/`, log, cache, sesi, atau upload lokal. Aturan ini sudah didefinisikan dalam [`.gitignore`](.gitignore). Setelah repository GitHub dibuat, alur dasarnya:

```bash
git init
git add .
git commit -m "Initial commit"
git branch -M main
git remote add origin https://github.com/USERNAME/petshop-apriori.git
git push -u origin main
```

## Lisensi

Proyek ini menggunakan lisensi MIT. Lihat [LICENSE](LICENSE).
