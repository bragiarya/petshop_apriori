<!-- Memanggil kerangka utama dari layout/template.php -->
<?= $this->extend('layout/template') ?>

<!-- Memasukkan semua kode di bawah ini ke dalam bagian 'content' di template -->
<?= $this->section('content') ?>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">📦 Master Data Produk</h5>
        <!-- Tombol untuk memunculkan Modal Tambah Produk -->
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
            + Tambah Produk
        </button>
    </div>
    <div class="card-body">
        
        <!-- Menampilkan pesan sukses dari Controller -->
        <?php if(session()->getFlashdata('pesan')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('pesan') ?></div>
        <?php endif; ?>

        <!-- Membuat tabel yang otomatis dikenali oleh DataTables via class 'datatable' -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped datatable" width="100%">
                <thead class="table-dark">
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Stok Saat Ini</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach($produk as $p): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= esc($p['nama_produk']) ?></td>
                        <!-- Format harga ke Rupiah -->
                        <td>Rp <?= number_format($p['harga'], 0, ',', '.') ?></td>
                        <td>
                            <?php if($p['stok'] == 0): ?>
                                <span class="badge bg-danger">Habis</span>
                            <?php else: ?>
                                <span class="badge bg-success"><?= $p['stok'] ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <!-- Tombol Edit & Hapus -->
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $p['id'] ?>">Edit</button>
                            <a href="<?= base_url('produk/delete/'.$p['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus produk ini beserta histori transaksinya (Cascade)?')">Hapus</a>
                        </td>
                    </tr>

                    <!-- MODAL EDIT PRODUK (Dibuat berulang untuk tiap baris data) -->
                    <div class="modal fade" id="modalEdit<?= $p['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <form action="<?= base_url('produk/update/'.$p['id']) ?>" method="post">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Produk</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label>Nama Produk</label>
                                            <input type="text" name="nama_produk" class="form-control" value="<?= esc($p['nama_produk']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label>Harga (Rp)</label>
                                            <input type="number" name="harga" class="form-control" value="<?= $p['harga'] ?>" required>
                                        </div>
                                        <small class="text-muted">*Stok hanya bisa diubah melalui menu Stok Masuk / Stok Keluar untuk keperluan riwayat.</small>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH PRODUK -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= base_url('produk/store') ?>" method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Produk Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nama Produk</label>
                        <input type="text" name="nama_produk" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Harga Jual (Rp)</label>
                        <input type="number" name="harga" class="form-control" required>
                    </div>
                    <small class="text-muted">*Produk baru otomatis memiliki stok 0.</small>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan Produk</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>