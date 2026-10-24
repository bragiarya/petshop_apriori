<!-- Mengambil layout master -->
<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<div class="row">
    <!-- Kolom Kiri: Form Kurangi Stok -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">📤 Catat Stok Keluar</h5>
            </div>
            <div class="card-body">
                <?php if(session()->getFlashdata('pesan')): ?>
                    <div class="alert alert-success"><?= session()->getFlashdata('pesan') ?></div>
                <?php endif; ?>

                <form action="<?= base_url('stok/keluar/simpan') ?>" method="post">
                    <div class="mb-3">
                        <label>Pilih Produk</label>
                        <select name="produk_id" class="form-select" required>
                            <option value="">-- Pilih Produk --</option>
                            <?php foreach($produk as $p): ?>
                                <!-- Hanya tampilkan produk yang stoknya > 0, karena tidak mungkin membuang barang yang stoknya 0 -->
                                <?php if($p['stok'] > 0): ?>
                                    <option value="<?= $p['id'] ?>"><?= esc($p['nama_produk']) ?> (Stok: <?= $p['stok'] ?>)</option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Jumlah Keluar (Qty)</label>
                        <input type="number" name="qty" class="form-control" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Keterangan Alasan</label>
                        <textarea name="keterangan" class="form-control" rows="2" placeholder="Contoh: Barang expired / kemasan rusak" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger w-100">Simpan Stok Keluar</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Tabel Riwayat Stok Keluar -->
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">📋 Riwayat Stok Keluar</h5>
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
                            <?php foreach($stok_keluar as $sk): ?>
                            <tr>
                                <td><?= date('d-m-Y', strtotime($sk['tanggal'])) ?></td>
                                <td><?= esc($sk['nama_produk']) ?></td>
                                <td><span class="badge bg-danger">-<?= $sk['qty'] ?></span></td>
                                <td><?= esc($sk['keterangan']) ?></td>
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