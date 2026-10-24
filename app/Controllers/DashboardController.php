<?php

namespace App\Controllers;

use App\Models\ProdukModel;
use App\Models\TransaksiModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $produkModel = new ProdukModel();
        $transaksiModel = new TransaksiModel();

        // Mengambil total ringkasan untuk kartu dashboard
        $data = [
            'title'          => 'Dashboard Petshop',
            'total_produk'   => $produkModel->countAllResults(),
            'total_transaksi'=> $transaksiModel->countAllResults(),
            'produk_habis'   => $produkModel->where('stok', 0)->countAllResults(),
            'grafik_stok'    => $produkModel->select('nama_produk, stok')->findAll() // Data untuk grafik Chart.js
        ];

        return view('dashboard/index', $data);
    }
}