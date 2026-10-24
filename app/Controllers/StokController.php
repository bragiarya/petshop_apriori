<?php

namespace App\Controllers;

use App\Models\StokMasukModel;
use App\Models\StokKeluarModel;
use App\Models\ProdukModel;

class StokController extends BaseController
{
    public function masuk()
    {
        // Menampilkan halaman riwayat stok masuk beserta daftar produk untuk pilihan di form.
        $stokMasukModel = new StokMasukModel();
        $produkModel = new ProdukModel();

        // Mengambil data stok masuk dan menggabungkannya (JOIN) dengan tabel produk agar kita tahu nama produknya.
        $db = \Config\Database::connect();
        $builder = $db->table('stok_masuk');
        $builder->select('stok_masuk.*, produk.nama_produk');
        $builder->join('produk', 'produk.id = stok_masuk.produk_id');
        $builder->orderBy('stok_masuk.tanggal', 'DESC');
        
        $data = [
            'title'      => 'Barang Masuk',
            'stok_masuk' => $builder->get()->getResultArray(),
            'produk'     => $produkModel->findAll()
        ];

        return view('stok/masuk', $data);
    }

    public function simpanMasuk()
    {
        // Memproses form penambahan stok masuk.
        $db = \Config\Database::connect();
        
        $produk_id = $this->request->getPost('produk_id');
        $qty       = $this->request->getPost('qty');

        // Memulai mode "Transaksi Database" (Keamanan Data)
        $db->transStart();

        // 1. Catat ke histori tabel stok_masuk
        $stokMasukModel = new StokMasukModel();
        $stokMasukModel->save([
            'produk_id'  => $produk_id,
            'qty'        => $qty,
            'tanggal'    => $this->request->getPost('tanggal'),
            'keterangan' => $this->request->getPost('keterangan')
        ]);

        // 2. Tambahkan jumlah qty ke total stok di tabel produk
        $produkModel = new ProdukModel();
        $produkLama = $produkModel->find($produk_id);
        $stokBaru = $produkLama['stok'] + $qty;
        
        $produkModel->save([
            'id'   => $produk_id,
            'stok' => $stokBaru
        ]);

        // Menyelesaikan transaksi
        $db->transComplete();

        return redirect()->to('/stok/masuk')->with('pesan', 'Stok masuk berhasil dicatat dan stok produk bertambah.');
    }

    public function keluar()
    {
        // Menampilkan halaman riwayat stok keluar.
        $produkModel = new ProdukModel();
        $db = \Config\Database::connect();
        
        $builder = $db->table('stok_keluar');
        $builder->select('stok_keluar.*, produk.nama_produk');
        $builder->join('produk', 'produk.id = stok_keluar.produk_id');
        $builder->orderBy('stok_keluar.tanggal', 'DESC');
        
        $data = [
            'title'       => 'Barang Keluar',
            'stok_keluar' => $builder->get()->getResultArray(),
            'produk'      => $produkModel->findAll()
        ];

        return view('stok/keluar', $data);
    }

    public function simpanKeluar()
    {
        // Memproses form pengurangan stok (misal barang expired).
        $db = \Config\Database::connect();
        
        $produk_id = $this->request->getPost('produk_id');
        $qty       = $this->request->getPost('qty');

        $db->transStart();

        // 1. Catat ke histori tabel stok_keluar
        $stokKeluarModel = new StokKeluarModel();
        $stokKeluarModel->save([
            'produk_id'  => $produk_id,
            'qty'        => $qty,
            'tanggal'    => $this->request->getPost('tanggal'),
            'keterangan' => $this->request->getPost('keterangan')
        ]);

        // 2. Kurangi jumlah qty dari total stok di tabel produk
        $produkModel = new ProdukModel();
        $produkLama = $produkModel->find($produk_id);
        $stokBaru = $produkLama['stok'] - $qty;
        
        // Pencegahan agar stok tidak menjadi minus
        if ($stokBaru < 0) {
            $stokBaru = 0;
        }

        $produkModel->save([
            'id'   => $produk_id,
            'stok' => $stokBaru
        ]);

        $db->transComplete();

        return redirect()->to('/stok/keluar')->with('pesan', 'Stok keluar berhasil dicatat dan stok produk berkurang.');
    }
}