<!-- Kode ini ngapain: Layout Dashboard -->
<?= $this->extend('layout/template') ?>
<?= $this->section('content') ?>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-white bg-primary shadow-sm">
            <div class="card-body">
                <h5 class="card-title">📦 Total Produk</h5>
                <h2 class="fw-bold"><?= $total_produk ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-success shadow-sm">
            <div class="card-body">
                <h5 class="card-title">🛒 Total Transaksi</h5>
                <h2 class="fw-bold"><?= $total_transaksi ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-danger shadow-sm">
            <div class="card-body">
                <h5 class="card-title">⚠️ Produk Stok Habis</h5>
                <h2 class="fw-bold"><?= $produk_habis ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white fw-bold">
        📈 Grafik Stok Produk Saat Ini
    </div>
    <div class="card-body">
        <canvas id="stokChart" height="100"></canvas>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const dataStok = <?= json_encode($grafik_stok) ?>;
    let labelStok = [];
    let nilaiStok = [];

    dataStok.forEach(item => {
        labelStok.push(item.nama_produk);
        nilaiStok.push(item.stok);
    });

    const ctxStok = document.getElementById('stokChart').getContext('2d');
    new Chart(ctxStok, {
        type: 'line', // Menggunakan grafik garis untuk variasi
        data: {
            labels: labelStok,
            datasets: [{
                label: 'Jumlah Stok',
                data: nilaiStok,
                borderColor: '#198754',
                backgroundColor: 'rgba(25, 135, 84, 0.2)',
                borderWidth: 2,
                fill: true
            }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });
</script>
<?= $this->endSection() ?>