<?php

namespace App\Controllers;

use App\Models\TransaksiModel;
use App\Models\DetailTransaksiModel;
use App\Models\ProdukModel;

class TransaksiController extends BaseController
{
    public function index()
    {
        // Menampilkan daftar semua transaksi/invoice yang pernah terjadi.
        $db = \Config\Database::connect();
        $builder = $db->table('transaksi');
        $builder->select('transaksi.*, GROUP_CONCAT(produk.nama_produk ORDER BY produk.nama_produk ASC SEPARATOR ",") as items_dibeli');
        $builder->join('detail_transaksi', 'detail_transaksi.transaksi_id = transaksi.id', 'left');
        $builder->join('produk', 'produk.id = detail_transaksi.produk_id', 'left');
        $builder->groupBy('transaksi.id');
        $builder->orderBy('transaksi.tanggal', 'DESC');

        $transaksiList = $builder->get()->getResultArray();

        // Ambil semua produk (untuk dropdown "Tambah Produk" di modal edit)
        $produkModel = new ProdukModel();
        $semuaProduk = $produkModel->orderBy('nama_produk', 'ASC')->findAll();

        // Ambil detail transaksi LENGKAP (termasuk qty) lalu kelompokkan per transaksi_id
        // supaya modal edit bisa pre-fill nama produk dan qty yang sudah dibeli
        $detailModel = new DetailTransaksiModel();
        $db2 = \Config\Database::connect();
        $detailLengkap = $db2->table('detail_transaksi')
            ->select('detail_transaksi.transaksi_id, detail_transaksi.produk_id, detail_transaksi.qty, produk.nama_produk, produk.harga, produk.stok')
            ->join('produk', 'produk.id = detail_transaksi.produk_id', 'left')
            ->get()->getResultArray();

        $detailLengkapPerTransaksi = [];
        foreach ($detailLengkap as $d) {
            $detailLengkapPerTransaksi[$d['transaksi_id']][] = $d;
        }

        $data = [
            'title'                      => 'Data Transaksi',
            'transaksi'                  => $transaksiList,
            'semuaProduk'                => $semuaProduk,
            'detailLengkapPerTransaksi'  => $detailLengkapPerTransaksi,
        ];
        return view('transaksi/index', $data);
    }

    public function buat()
    {
        // Menampilkan form halaman kasir untuk membuat transaksi baru.
        $produkModel = new ProdukModel();
        $data = [
            'title'  => 'Buat Transaksi Baru',
            // Hanya ambil produk yang stoknya lebih dari 0 agar tidak bisa jual barang kosong
            'produk' => $produkModel->where('stok >', 0)->findAll() 
        ];
        return view('transaksi/buat', $data);
    }

    public function simpan()
    {
        // Memproses keranjang belanja dari kasir.
        $db = \Config\Database::connect();
        $db->transStart();

        $transaksiModel = new TransaksiModel();
        $detailModel = new DetailTransaksiModel();
        $produkModel = new ProdukModel();

        // 1. Simpan data utama ke tabel transaksi
        $transaksiModel->save([
            'no_invoice'  => $this->request->getPost('no_invoice'),
            'tanggal'     => $this->request->getPost('tanggal'),
            'total_harga' => $this->request->getPost('total_harga')
        ]);
        
        // Mengambil ID transaksi yang baru saja tersimpan untuk diikatkan ke tabel detail
        $transaksi_id = $transaksiModel->insertID();

        // 2. Looping data array produk_id dan qty yang dikirim dari form keranjang
        $produk_ids = $this->request->getPost('produk_id');
        $qtys = $this->request->getPost('qty');
        $hargas = $this->request->getPost('harga_satuan');

        if ($produk_ids) {
            for ($i = 0; $i < count($produk_ids); $i++) {
                
                // Simpan tiap item ke detail_transaksi
                $detailModel->save([
                    'transaksi_id' => $transaksi_id,
                    'produk_id'    => $produk_ids[$i],
                    'qty'          => $qtys[$i],
                    'harga_satuan' => $hargas[$i]
                ]);

                // Kurangi stok produknya karena sudah laku terjual
                $produkLama = $produkModel->find($produk_ids[$i]);
                $produkModel->save([
                    'id'   => $produk_ids[$i],
                    'stok' => $produkLama['stok'] - $qtys[$i]
                ]);
            }
        }

        $db->transComplete();

        return redirect()->to('/transaksi')->with('pesan', 'Transaksi berhasil disimpan!');
    }

    public function edit($id)
    {
        // Memproses edit transaksi: tanggal + item + qty, dengan validasi stok
        $transaksiModel = new TransaksiModel();
        $detailModel    = new DetailTransaksiModel();
        $produkModel    = new ProdukModel();

        $produk_ids_baru = $this->request->getPost('produk_id') ?? [];
        $qtys_baru       = $this->request->getPost('qty')       ?? [];

        // Pastikan minimal ada 1 item
        if (empty($produk_ids_baru)) {
            return redirect()->to('/transaksi')->with('error', 'Transaksi harus memiliki minimal 1 item produk.');
        }

        // ── TAHAP 1: VALIDASI STOK (tanpa mengubah database) ─────────────────
        // Kumpulkan qty lama per produk agar bisa dihitung stok_tersedia
        $detailLama = $detailModel->where('transaksi_id', $id)->findAll();
        $qtyLamaPerProduk = [];
        foreach ($detailLama as $d) {
            $qtyLamaPerProduk[$d['produk_id']] = (int) $d['qty'];
        }

        // Gabungkan produk_id dan qty baru menjadi pasangan yang unik
        // (jika user mengirim produk yang sama 2x dari form, gabungkan qty-nya)
        $itemBaru = [];
        foreach ($produk_ids_baru as $i => $pid) {
            $pid = (int) $pid;
            $qty = (int) ($qtys_baru[$i] ?? 1);
            if ($qty < 1) { $qty = 1; }
            if (isset($itemBaru[$pid])) {
                $itemBaru[$pid] += $qty;
            } else {
                $itemBaru[$pid] = $qty;
            }
        }

        foreach ($itemBaru as $pid => $qty_baru) {
            $produk = $produkModel->find($pid);
            if (!$produk) {
                return redirect()->to('/transaksi')->with('error', 'Produk dengan ID ' . $pid . ' tidak ditemukan.');
            }

            // Stok tersedia = stok saat ini di gudang + stok yang pernah dialokasikan oleh transaksi ini
            $qty_lama_produk_ini  = $qtyLamaPerProduk[$pid] ?? 0;
            $stok_tersedia        = (int) $produk['stok'] + $qty_lama_produk_ini;

            if ($qty_baru > $stok_tersedia) {
                return redirect()->to('/transaksi')->with(
                    'error',
                    'Stok <strong>' . esc($produk['nama_produk']) . '</strong> tidak mencukupi! '
                    . 'Dibutuhkan: <strong>' . $qty_baru . ' pcs</strong>, '
                    . 'Tersedia: <strong>' . $stok_tersedia . ' pcs</strong>.'
                );
            }
        }

        // ── TAHAP 2: EKSEKUSI (semua validasi lolos, baru ubah database) ─────
        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Kembalikan stok semua item lama
        foreach ($detailLama as $d) {
            $produk = $produkModel->find($d['produk_id']);
            if ($produk) {
                $produkModel->save([
                    'id'   => $d['produk_id'],
                    'stok' => (int) $produk['stok'] + (int) $d['qty'],
                ]);
            }
        }

        // 2. Hapus semua detail lama
        $detailModel->where('transaksi_id', $id)->delete();

        // 3. Simpan item baru, kurangi stok, hitung total harga
        $total_harga_baru = 0;
        foreach ($itemBaru as $pid => $qty_baru) {
            $produk = $produkModel->find($pid);

            $produkModel->save([
                'id'   => $pid,
                'stok' => (int) $produk['stok'] - $qty_baru,
            ]);

            $detailModel->save([
                'transaksi_id' => $id,
                'produk_id'    => $pid,
                'qty'          => $qty_baru,
                'harga_satuan' => $produk['harga'],
            ]);

            $total_harga_baru += $produk['harga'] * $qty_baru;
        }

        // 4. Update tabel transaksi
        $transaksiModel->update($id, [
            'tanggal'     => $this->request->getPost('tanggal'),
            'total_harga' => $total_harga_baru,
        ]);

        $db->transComplete();

        return redirect()->to('/transaksi')->with('pesan', 'Transaksi berhasil diperbarui.');
    }

    public function delete($id)
    {
        // Memproses hapus transaksi dan mengembalikan stok produk
        $db = \Config\Database::connect();
        $db->transStart();

        $transaksiModel = new TransaksiModel();
        $detailModel = new DetailTransaksiModel();
        $produkModel = new ProdukModel();

        // 1. Ambil rincian detail transaksi untuk mengembalikan stok produk
        $details = $detailModel->where('transaksi_id', $id)->findAll();
        foreach ($details as $d) {
            $produk = $produkModel->find($d['produk_id']);
            if ($produk) {
                $produkModel->save([
                    'id'   => $d['produk_id'],
                    'stok' => $produk['stok'] + $d['qty']
                ]);
            }
        }

        // 2. Hapus data transaksi utama (Cascade database akan menghapus detail_transaksi otomatis)
        $transaksiModel->delete($id);

        $db->transComplete();

        return redirect()->to('/transaksi')->with('pesan', 'Transaksi berhasil dihapus dan stok produk telah dikembalikan.');
    }

    public function prosesImport()
    {
        // 1. VALIDASI FILE
        $rules = [
            'file_csv' => [
                'rules'  => 'uploaded[file_csv]|ext_in[file_csv,csv]|max_size[file_csv,2048]',
                'errors' => [
                    'uploaded' => 'Anda belum memilih file.',
                    'ext_in'   => 'Format file harus berupa .csv (Save As -> CSV di Excel).',
                    'max_size' => 'Ukuran file maksimal 2 MB.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/transaksi')->with('error', $this->validator->getErrors()['file_csv']);
        }

        // 2. BACA ISI FILE CSV
        $file = $this->request->getFile('file_csv');
        $filepath = $file->getTempName();
        $handle = fopen($filepath, "r");
        
        $header = fgetcsv($handle, 1000, ",");
        if ($header === FALSE) {
            fclose($handle);
            return redirect()->to('/transaksi')->with('error', 'File CSV kosong.');
        }
        
        // Menghapus karakter "hantu" BOM yang sering menempel di file CSV buatan Microsoft Excel
        $header[0] = preg_replace('/\xEF\xBB\xBF/', '', $header[0]);
        
        if (trim($header[0]) !== 'Tanggal' || trim($header[1]) !== 'ID_Transaksi' || trim($header[2]) !== 'Item') {
            fclose($handle);
            return redirect()->to('/transaksi')->with('error', 'Format salah! Urutan kolom harus: <b>Tanggal, ID_Transaksi, Item</b>.');
        }

        // 3. PROSES IMPORT
        $transaksiModel = new TransaksiModel();
        $detailModel    = new DetailTransaksiModel();
        $produkModel    = new ProdukModel();
        $db             = \Config\Database::connect();

        // Kosongkan data lama jika dicentang oleh user
        if ($this->request->getPost('truncate_old') === '1') {
            $db->query("SET FOREIGN_KEY_CHECKS = 0;");
            $db->table('detail_transaksi')->truncate();
            $db->table('transaksi')->truncate();
            $db->query("SET FOREIGN_KEY_CHECKS = 1;");
        }

        $berhasil = 0;
        $gagal    = 0;

        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
            
            if (count($row) < 3) {
                $gagal++;
                continue; 
            }

            $tanggal    = date('Y-m-d', strtotime($row[0]));
            $no_invoice = $row[1];
            
            // Mengambil semua item mulai dari indeks ke-2 hingga kolom terakhir baris CSV
            $array_items = [];
            $raw_items = array_slice($row, 2);
            foreach ($raw_items as $item_part) {
                $exploded = explode(',', $item_part);
                foreach ($exploded as $ex) {
                    $trimmed = trim($ex);
                    if ($trimmed !== '') {
                        $array_items[] = $trimmed;
                    }
                }
            }
            
            $total_harga = 0;
            $produk_yang_dibeli = [];
            $baris_valid = true;

            foreach ($array_items as $item_nama) {
                $item_nama = trim($item_nama); 
                $produk = $produkModel->where('nama_produk', $item_nama)->first();
                
                if (!$produk) {
                    $baris_valid = false;
                    break;
                }

                $produk_yang_dibeli[] = [
                    'produk_id'    => $produk['id'],
                    'harga_satuan' => $produk['harga']
                ];
                $total_harga += $produk['harga'];
            }

            if (!$baris_valid) {
                $gagal++;
                continue;
            }

            $db->transStart(); 
            
            $transaksiModel->save([
                'no_invoice'  => $no_invoice,
                'tanggal'     => $tanggal,
                'total_harga' => $total_harga
            ]);
            $transaksi_id = $transaksiModel->insertID();

            foreach ($produk_yang_dibeli as $p) {
                $detailModel->save([
                    'transaksi_id' => $transaksi_id,
                    'produk_id'    => $p['produk_id'],
                    'qty'          => 1, 
                    'harga_satuan' => $p['harga_satuan']
                ]);
            }
            
            $db->transComplete(); 
            $berhasil++; 
        }

        fclose($handle);

        return redirect()->to('/transaksi')->with('pesan', "Import selesai! <strong>$berhasil</strong> baris masuk ke database. <strong>$gagal</strong> baris dilewati.");
    }
}