<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Petshop Apriori' ?></title>
    
    <!-- Mengambil file CSS dari Bootstrap 5 secara online (CDN) agar tampilan rapi -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Mengambil CSS untuk DataTables agar tabel kita punya fitur Search dan Pagination otomatis -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <!-- Memanggil library Chart.js untuk menampilkan grafik di tahap selanjutnya -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body { background-color: #f8f9fa; }
        .navbar-brand { font-weight: bold; }
    </style>
</head>
<body>

    <!-- Membuat Menu Navigasi (Navbar) di bagian atas layar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url('/dashboard') ?>">🐾 Petshop Apriori</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('produk') ?>">Produk</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Manajemen Stok</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= base_url('stok/masuk') ?>">Stok Masuk</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('stok/keluar') ?>">Stok Keluar</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Transaksi</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= base_url('transaksi/buat') ?>">Kasir (Buat Baru)</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('transaksi') ?>">Riwayat Transaksi</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-bold text-warning" href="<?= base_url('apriori') ?>">Analisis Apriori</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="btn btn-danger fw-bold" href="<?= base_url('logout') ?>" onclick="return confirm('Yakin ingin keluar?')">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Menyiapkan 'ruang kosong' di mana konten dari halaman lain akan dimasukkan -->
    <div class="container">
        <?= $this->renderSection('content') ?>
    </div>

    <!-- Memanggil file Javascript yang dibutuhkan oleh Bootstrap dan DataTables -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Mengaktifkan DataTables pada setiap tabel yang memiliki class 'datatable' -->
    <script>
        $(document).ready(function() {
            $('.datatable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' // Mengubah teks tabel jadi Bahasa Indonesia
                }
            });
        });
    </script>

    <!-- Ruang kosong untuk tambahan Javascript spesifik di halaman tertentu -->
    <?= $this->renderSection('scripts') ?>
</body>
</html>