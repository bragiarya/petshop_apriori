<?= $this->extend('layout/template') ?>
<?= $this->section('content') ?>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">💳 Form Kasir (Transaksi Baru)</h5>
    </div>
    <div class="card-body">
        <form action="<?= base_url('transaksi/simpan') ?>" method="post">
            
            <!-- Informasi Utama Invoice -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <label>No Invoice</label>
                    <!-- Membuat nomor invoice otomatis berdasarkan angka random -->
                    <input type="text" name="no_invoice" class="form-control" value="INV-<?= time() ?>" readonly>
                </div>
                <div class="col-md-6">
                    <label>Tanggal Transaksi</label>
                    <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
            </div>

            <!-- Area Keranjang Belanja -->
            <h5 class="border-bottom pb-2">Keranjang Belanja</h5>
            <div class="table-responsive">
                <table class="table table-bordered" id="tabelKeranjang">
                    <thead class="table-light">
                        <tr>
                            <th width="40%">Pilih Produk</th>
                            <th width="20%">Harga Satuan</th>
                            <th width="15%">Qty</th>
                            <th width="20%">Subtotal</th>
                            <th width="5%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Baris pertama dibiarkan kosong untuk diisi oleh Javascript -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end fw-bold">TOTAL KESELURUHAN</td>
                            <td>
                                <!-- Input tersembunyi untuk dikirim ke Controller -->
                                <input type="hidden" name="total_harga" id="total_harga_input" value="0">
                                <!-- Tampilan teks tebal untuk user -->
                                <h5 class="text-success fw-bold mb-0" id="total_harga_tampil">Rp 0</h5>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info text-white" id="btnTambahBaris">+ Tambah</button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="text-end mt-3">
                <a href="<?= base_url('transaksi') ?>" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary fw-bold px-4">Simpan & Bayar</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<!-- Menyisipkan Javascript khusus untuk halaman kasir ini saja (karena di template ada renderSection('scripts')) -->
<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    
    // Menyimpan daftar produk dari PHP ke dalam variabel Javascript agar bisa digunakan berulang kali
    let produkData = [
        <?php foreach($produk as $p): ?>
            { id: '<?= $p['id'] ?>', nama: '<?= addslashes($p['nama_produk']) ?>', harga: <?= $p['harga'] ?>, stok: <?= $p['stok'] ?> },
        <?php endforeach; ?>
    ];

    // Fungsi untuk menambahkan baris kosong baru di tabel keranjang
    function tambahBaris() {
        let opsiProduk = '<option value="">-- Pilih --</option>';
        produkData.forEach(function(p) {
            opsiProduk += `<option value="${p.id}" data-harga="${p.harga}" data-stok="${p.stok}">${p.nama} (Stok: ${p.stok})</option>`;
        });

        let barisHTML = `
            <tr>
                <td>
                    <select name="produk_id[]" class="form-select select-produk" required>
                        ${opsiProduk}
                    </select>
                </td>
                <td>
                    <input type="number" name="harga_satuan[]" class="form-control input-harga" readonly required>
                </td>
                <td>
                    <input type="number" name="qty[]" class="form-control input-qty" value="1" min="1" required>
                </td>
                <td>
                    <input type="text" class="form-control input-subtotal" readonly>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm btn-hapus-baris">X</button>
                </td>
            </tr>
        `;
        $('#tabelKeranjang tbody').append(barisHTML);
    }

    // Fungsi untuk menghitung total keseluruhan harga di keranjang
    function hitungTotal() {
        let total = 0;
        $('.input-subtotal').each(function() {
            let sub = $(this).val().replace(/[^0-9]/g, ''); // Hilangkan huruf/simbol, ambil angkanya saja
            if(sub) total += parseInt(sub);
        });
        
        // Update input hidden dan teks total
        $('#total_harga_input').val(total);
        $('#total_harga_tampil').text('Rp ' + new Intl.NumberFormat('id-ID').format(total));
    }

    // Panggil 1 baris kosong saat halaman pertama kali dibuka
    tambahBaris();

    // Event saat tombol "+ Tambah" diklik
    $('#btnTambahBaris').click(function() {
        tambahBaris();
    });

    // Event saat tombol "X" (Hapus baris) diklik
    $(document).on('click', '.btn-hapus-baris', function() {
        $(this).closest('tr').remove();
        hitungTotal(); // Hitung ulang total setelah ada baris yang dihapus
    });

    // Event saat produk dipilih di dropdown
    $(document).on('change', '.select-produk', function() {
        let harga = $(this).find('option:selected').data('harga');
        let stok = $(this).find('option:selected').data('stok');
        let baris = $(this).closest('tr');
        
        if (harga) {
            baris.find('.input-harga').val(harga);
            // Cek subtotal
            let qty = baris.find('.input-qty').val();
            let subtotal = harga * qty;
            baris.find('.input-subtotal').val('Rp ' + new Intl.NumberFormat('id-ID').format(subtotal));
            // Batasi atribut max qty berdasarkan stok
            baris.find('.input-qty').attr('max', stok);
        } else {
            baris.find('.input-harga').val('');
            baris.find('.input-subtotal').val('');
        }
        hitungTotal();
    });

    // Event saat jumlah (qty) diubah
    $(document).on('keyup change', '.input-qty', function() {
        let baris = $(this).closest('tr');
        let harga = baris.find('.input-harga').val();
        let qty = $(this).val();
        
        if(harga && qty) {
            let subtotal = harga * qty;
            baris.find('.input-subtotal').val('Rp ' + new Intl.NumberFormat('id-ID').format(subtotal));
        }
        hitungTotal();
    });

});
</script>
<?= $this->endSection() ?>