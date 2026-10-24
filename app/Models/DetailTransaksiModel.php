<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailTransaksiModel extends Model
{
    protected $table            = 'detail_transaksi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    
    // Mengizinkan pengisian ID transaksi, ID produk, jumlah beli, dan harga satuan
    protected $allowedFields    = ['transaksi_id', 'produk_id', 'qty', 'harga_satuan'];

    protected $useTimestamps    = true;
}