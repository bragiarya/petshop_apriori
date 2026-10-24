<?= $this->extend('layout/template') ?>
<?= $this->section('content') ?>

<style>
    /* CSS standar untuk area dropzone */
    .dropzone {
        border: 2px dashed #0d6efd;
        border-radius: 10px;
        padding: 30px;
        text-align: center;
        background-color: #f8f9fa;
        cursor: pointer;
        transition: all 0.3s;
    }
    .dropzone.dragover {
        background-color: #e9ecef;
        border-color: #198754;
    }
</style>

<?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>
<?php if(session()->getFlashdata('pesan')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('pesan') ?></div>
<?php endif; ?>

<!-- CARD RIWAYAT TRANSAKSI -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">🛒 Riwayat Transaksi (Penjualan)</h5>
        <div>
            <a href="<?= base_url('transaksi/buat') ?>" class="btn btn-primary btn-sm">+ Buat Transaksi Baru</a>
            <button type="button" class="btn btn-success btn-sm ms-2"
                    data-bs-toggle="modal" data-bs-target="#modalImportCSV">
                📥 Import CSV
            </button>
        </div>
    </div>
    <div class="card-body">



        <div class="table-responsive">
            <table class="table table-bordered table-striped datatable" width="100%">
                <thead class="table-dark">
                    <tr>
                        <th width="5%">No</th>
                        <th>No Invoice</th>
                        <th>Tanggal</th>
                        <th>Item Dibeli</th>
                        <th>Total Harga</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach($transaksi as $t): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td class="fw-bold"><?= esc($t['no_invoice']) ?></td>
                        <td><?= date('d-m-Y', strtotime($t['tanggal'])) ?></td>
                        <td>
                            <?php 
                            if (!empty($t['items_dibeli'])):
                                $items = explode(',', $t['items_dibeli']);
                                foreach ($items as $item): 
                            ?>
                                    <span class="badge bg-primary me-1"><?= esc($item) ?></span>
                            <?php 
                                endforeach;
                            endif; 
                            ?>
                        </td>
                        <td class="text-success fw-bold">Rp <?= number_format($t['total_harga'], 0, ',', '.') ?></td>
                        <td>
                            <!-- Tombol Edit Modal -->
                            <button type="button" class="btn btn-warning btn-sm me-1" data-bs-toggle="modal" data-bs-target="#editModal<?= $t['id'] ?>">
                                Edit
                            </button>
                            <!-- Tombol Hapus -->
                            <a href="<?= base_url('transaksi/delete/' . $t['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus transaksi ini? Semua stok produk pada transaksi ini akan dikembalikan.')">
                                Hapus
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL EDIT TRANSAKSI — OPSI A (Tabel Dinamis + Dropdown) -->
<?php foreach($transaksi as $t): ?>
<?php
// Siapkan data item saat ini untuk transaksi ini
$itemSaatIni = $detailLengkapPerTransaksi[$t['id']] ?? [];
?>
<div class="modal fade" id="editModal<?= $t['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $t['id'] ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning-subtle">
                <h5 class="modal-title" id="editModalLabel<?= $t['id'] ?>">✏️ Edit Transaksi — <span class="text-primary fw-bold"><?= esc($t['no_invoice']) ?></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('transaksi/edit/' . $t['id']) ?>" method="post" id="formEdit<?= $t['id'] ?>">
                <?= csrf_field() ?>
                <div class="modal-body">

                    <!-- FIELD TANGGAL -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Tanggal Transaksi</label>
                        <input type="date" name="tanggal" class="form-control" value="<?= $t['tanggal'] ?>" required>
                    </div>

                    <!-- TABEL ITEM DINAMIS -->
                    <label class="form-label fw-bold">Item Dibeli</label>
                    <div class="table-responsive mb-2">
                        <table class="table table-bordered table-sm align-middle mb-0" id="tabelItem<?= $t['id'] ?>">
                            <thead class="table-secondary">
                                <tr>
                                    <th>Produk</th>
                                    <th width="110">Harga Satuan</th>
                                    <th width="90">Qty</th>
                                    <th width="120">Subtotal</th>
                                    <th width="50"></th>
                                </tr>
                            </thead>
                            <tbody id="tbodyItem<?= $t['id'] ?>">
                            <?php foreach ($itemSaatIni as $item): ?>
                                <tr>
                                    <td>
                                        <?= esc($item['nama_produk']) ?>
                                        <input type="hidden" name="produk_id[]" value="<?= $item['produk_id'] ?>">
                                        <input type="hidden" class="harga-satuan" value="<?= $item['harga'] ?>">
                                    </td>
                                    <td class="text-end text-success fw-semibold">Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
                                    <td>
                                        <input type="number" name="qty[]" class="form-control form-control-sm qty-input text-center" value="<?= $item['qty'] ?>" min="1" required>
                                    </td>
                                    <td class="text-end fw-semibold subtotal-cell">Rp <?= number_format($item['harga'] * $item['qty'], 0, ',', '.') ?></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm btn-hapus-baris" title="Hapus item ini">🗑</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="table-light fw-bold">
                                    <td colspan="3" class="text-end">Total Harga Baru:</td>
                                    <td class="text-end text-success" id="totalHarga<?= $t['id'] ?>">—</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- AREA TAMBAH PRODUK BARU -->
                    <div class="d-flex gap-2 align-items-center mt-3">
                        <select class="form-select form-select-sm select-tambah-produk" id="selectTambah<?= $t['id'] ?>" style="max-width:300px;">
                            <option value="">— Pilih produk untuk ditambahkan —</option>
                            <?php foreach ($semuaProduk as $p): ?>
                            <option value="<?= $p['id'] ?>" data-nama="<?= esc($p['nama_produk']) ?>" data-harga="<?= $p['harga'] ?>" data-stok="<?= $p['stok'] ?>">
                                <?= esc($p['nama_produk']) ?> (Stok: <?= $p['stok'] ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" class="btn btn-outline-primary btn-sm btn-tambah-produk" data-target="<?= $t['id'] ?>">
                            + Tambah
                        </button>
                    </div>
                    <div class="alert alert-info py-2 mt-3 mb-0 small">
                        ℹ️ Stok akan dihitung ulang otomatis. Jika stok tidak mencukupi, perubahan akan dibatalkan dan Anda akan mendapat notifikasi.
                    </div>

                </div><!-- /modal-body -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<div class="modal fade" id="modalImportCSV" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">📥 Import Data Transaksi (CSV)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning py-2 mb-3">
                    <small>
                        <strong>Format Wajib:</strong> File wajib ber-ekstensi <b>.csv</b> dengan urutan header <code>Tanggal, ID_Transaksi, Item</code>. Maksimal 2MB.
                    </small>
                </div>

                <form action="<?= base_url('transaksi/proses-import') ?>" method="post" enctype="multipart/form-data" id="formImport">
                    <?= csrf_field() ?>
                    
                    <!-- Kotak area Dropzone -->
                    <div id="dropzone" class="dropzone mb-3">
                        <h5 class="text-primary mb-1" id="dropTitle">Seret & Lepas file CSV di sini</h5>
                        <p class="text-muted mb-2" id="dropDesc">atau klik untuk memilih file dari komputer</p>
                        <button type="button" class="btn btn-primary btn-sm">Import CSV</button>
                    </div>

                    <!-- Opsi Kosongkan Data Lama -->
                    <div class="form-check mb-3 p-3 border border-danger-subtle rounded bg-danger-subtle bg-opacity-10">
                        <input class="form-check-input ms-0 me-2" type="checkbox" name="truncate_old" id="truncate_old" value="1">
                        <label class="form-check-label text-danger fw-bold shadow-none" for="truncate_old">
                            ⚠️ Kosongkan semua data transaksi lama sebelum mengimpor data baru
                        </label>
                        <div class="form-text text-muted ms-4">
                            Jika dicentang, semua riwayat transaksi lama pada database (tabel <code>transaksi</code> dan <code>detail_transaksi</code>) akan dihapus secara permanen sebelum dataset baru diunggah.
                        </div>
                    </div>

                    <!-- Input file disembunyikan -->
                    <input type="file" name="file_csv" id="file_csv" accept=".csv" class="d-none" required>

                    <div id="previewArea" class="d-none">
                        <h6 class="fw-bold text-success mt-3">👀 Preview 5 Baris Pertama:</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm mb-0" id="previewTable">
                                <thead class="table-light" id="previewHead"></thead>
                                <tbody id="previewBody"></tbody>
                            </table>
                        </div>
                    </div>

                    <div class="text-end mt-3 d-none" id="btnArea">
                        <button type="button" class="btn btn-secondary me-2" id="btnBatal">Batal</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" form="formImport" class="btn btn-success fw-bold px-4">Mulai Import</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {

    // ──────────────────────────────────────────────────────────────────────────
    // FUNGSI HELPER UNTUK MODAL EDIT TRANSAKSI
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Format angka menjadi string "Rp 1.000.000"
     */
    function formatRupiah(angka) {
        return 'Rp ' + parseInt(angka).toLocaleString('id-ID');
    }

    /**
     * Hitung ulang total harga pada modal tertentu
     * dan perbarui tampilan kolom subtotal tiap baris.
     */
    function hitungUlangTotal(modalId) {
        let total = 0;
        $('#tbodyItem' + modalId + ' tr').each(function() {
            const harga = parseFloat($(this).find('.harga-satuan').val()) || 0;
            const qty   = parseInt($(this).find('.qty-input').val()) || 1;
            const sub   = harga * qty;
            $(this).find('.subtotal-cell').text(formatRupiah(sub));
            total += sub;
        });
        $('#totalHarga' + modalId).text(formatRupiah(total));
    }

    // Hitung total awal untuk setiap modal saat halaman pertama kali dimuat
    <?php foreach ($transaksi as $t): ?>
    hitungUlangTotal(<?= $t['id'] ?>);
    <?php endforeach; ?>

    // ── Event: perubahan Qty di input → hitung ulang total ────────────────────
    $(document).on('input', '.qty-input', function() {
        const modalId = $(this).closest('.modal').attr('id').replace('editModal', '');
        hitungUlangTotal(modalId);
    });

    // ── Event: Tombol Hapus Baris ─────────────────────────────────────────────
    $(document).on('click', '.btn-hapus-baris', function() {
        const tbody  = $(this).closest('tbody');
        const modalId = $(this).closest('.modal').attr('id').replace('editModal', '');

        // Minimal harus ada 1 baris tersisa
        if (tbody.find('tr').length <= 1) {
            alert('Transaksi harus memiliki minimal 1 item produk.');
            return;
        }
        $(this).closest('tr').remove();
        hitungUlangTotal(modalId);
    });

    // ── Event: Tombol Tambah Produk ───────────────────────────────────────────
    $(document).on('click', '.btn-tambah-produk', function() {
        const modalId  = $(this).data('target');
        const select   = $('#selectTambah' + modalId);
        const option   = select.find('option:selected');

        if (!option.val()) {
            alert('Pilih produk terlebih dahulu.');
            return;
        }

        const pid   = option.val();
        const nama  = option.data('nama');
        const harga = option.data('harga');
        const stok  = option.data('stok');

        // Cek apakah produk ini sudah ada di tabel (cegah duplikat)
        let sudahAda = false;
        $('#tbodyItem' + modalId + ' input[name="produk_id[]"]').each(function() {
            if ($(this).val() == pid) {
                sudahAda = true;
                return false; // break
            }
        });

        if (sudahAda) {
            alert('"' + nama + '" sudah ada di daftar. Ubah Qty-nya langsung di tabel.');
            return;
        }

        if (stok < 1) {
            alert('Stok "' + nama + '" sudah habis (0 pcs). Produk tidak dapat ditambahkan.');
            return;
        }

        const barisBaru = `
            <tr>
                <td>
                    ${nama}
                    <input type="hidden" name="produk_id[]" value="${pid}">
                    <input type="hidden" class="harga-satuan" value="${harga}">
                </td>
                <td class="text-end text-success fw-semibold">${formatRupiah(harga)}</td>
                <td>
                    <input type="number" name="qty[]" class="form-control form-control-sm qty-input text-center" value="1" min="1" max="${stok}" required>
                </td>
                <td class="text-end fw-semibold subtotal-cell">${formatRupiah(harga)}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm btn-hapus-baris" title="Hapus item ini">🗑</button>
                </td>
            </tr>`;

        $('#tbodyItem' + modalId).append(barisBaru);
        select.val(''); // Reset dropdown
        hitungUlangTotal(modalId);
    });

    // ──────────────────────────────────────────────────────────────────────────
    // FUNGSI DROPZONE IMPORT CSV
    // ──────────────────────────────────────────────────────────────────────────
    const dropzone   = $('#dropzone');
    const fileInput  = $('#file_csv');
    const previewArea = $('#previewArea');
    const btnArea    = $('#btnArea');
    const dropTitle  = $('#dropTitle');
    const dropDesc   = $('#dropDesc');

    // 1. Jika kotak diklik, maka otomatis mengklik input file yang tersembunyi
    dropzone.on('click', function() {
        fileInput.trigger('click');
    });

    // 2. Mencegah browser membuka file di tab baru saat di drag
    dropzone.on('dragover dragenter', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).addClass('dragover');
    });

    dropzone.on('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('dragover');
    });

    // 3. Menangkap file yang dijatuhkan (Drop)
    dropzone.on('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('dragover');
        
        let files = e.originalEvent.dataTransfer.files;
        if(files.length > 0) {
            // Memasukkan file yang di-drop ke dalam elemen <input> secara paksa
            fileInput[0].files = files; 
            fileInput.trigger('change'); // Memicu event change agar preview berjalan
        }
    });

    // 4. Proses membaca file setelah di-klik atau di-drop
    fileInput.on('change', function() {
        if(this.files && this.files.length > 0) {
            let file = this.files[0];
            
            if(!file.name.toLowerCase().endsWith('.csv')) {
                alert('Tolong pastikan file berakhiran .csv (Bukan .xlsx)');
                resetForm();
                return;
            }

            dropTitle.html(`✅ File terpilih: ${file.name}`).removeClass('text-primary').addClass('text-success');
            dropDesc.html(`Ukuran: ${(file.size/1024).toFixed(2)} KB`);

            bacaFilePreview(file);
        }
    });

    function bacaFilePreview(file) {
        let reader = new FileReader();
        reader.onload = function(e) {
            let isiText = e.target.result;
            // Memisahkan baris (\r\n untuk windows, \n untuk linux)
            let baris = isiText.split(/\r?\n/); 
            
            let htmlHead = '';
            let htmlBody = '';

            let maxRows = baris.length > 6 ? 6 : baris.length;

            for(let i = 0; i < maxRows; i++) {
                if(baris[i].trim() === '') continue; 
                
                let sel = baris[i].split(','); 
                
                let tr = '<tr>';
                for(let j = 0; j < sel.length; j++) {
                    if(i === 0) {
                        tr += `<th>${sel[j].trim()}</th>`; 
                    } else {
                        tr += `<td>${sel[j].trim()}</td>`; 
                    }
                }
                tr += '</tr>';

                if(i === 0) htmlHead += tr;
                else htmlBody += tr;
            }

            $('#previewHead').html(htmlHead);
            $('#previewBody').html(htmlBody);
            
            previewArea.removeClass('d-none');
            btnArea.removeClass('d-none');
        };
        reader.readAsText(file);
    }

    // 5. Tombol batal
    $('#btnBatal').click(function(e) {
        e.stopPropagation(); // Mencegah memicu klik pada parent dropzone
        resetForm();
    });

    function resetForm() {
        fileInput.val('');
        dropTitle.html('Seret & Lepas file CSV di sini').removeClass('text-success').addClass('text-primary');
        dropDesc.html('atau klik untuk memilih file dari komputer');
        
        previewArea.addClass('d-none');
        btnArea.addClass('d-none');
        $('#previewHead').html('');
        $('#previewBody').html('');
    }
});
</script>
<?= $this->endSection() ?>