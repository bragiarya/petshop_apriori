<!-- Mengambil layout master -->
<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<div class="row">
    <!-- Kolom Kiri: Form Tambah Stok -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">📥 Catat Stok Masuk</h5>
            </div>
            <div class="card-body">
                <!-- Menampilkan pesan sukses dari Controller -->
                <?php if(session()->getFlashdata('pesan')): ?>
                    <div class="alert alert-success"><?= session()->getFlashdata('pesan') ?></div>
                <?php endif; ?>

                <form action="<?= base_url('stok/masuk/simpan') ?>" method="post">
                    <div class="mb-3">
                        <label>Pilih Produk</label>
                        <!-- Menampilkan daftar produk dari database (dropdown) -->
                        <select name="produk_id" class="form-select" required>
                            <option value="">-- Pilih Produk --</option>
                            <?php foreach($produk as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= esc($p['nama_produk']) ?> (Stok saat ini: <?= $p['stok'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Jumlah Masuk (Qty)</label>
                        <input type="number" name="qty" class="form-control" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2" placeholder="Contoh: Dari Supplier A"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Simpan Stok Masuk</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Tabel Riwayat Stok Masuk -->
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">📋 Riwayat Stok Masuk</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped datatable" width="100%">
                        <thead class="table-dark">
                            <tr>
                                <th>Tanggal</th>
                                <th>Nama Produk</th>
                                <th>Qty</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($stok_masuk as $sm): ?>
                            <tr>
                                <td><?= date('d-m-Y', strtotime($sm['tanggal'])) ?></td>
                                <td><?= esc($sm['nama_produk']) ?></td>
                                <td><span class="badge bg-success">+<?= $sm['qty'] ?></span></td>
                                <td><?= esc($sm['keterangan']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>