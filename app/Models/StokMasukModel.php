<?php

namespace App\Models;

use CodeIgniter\Model;

class StokMasukModel extends Model
{
    protected $table            = 'stok_masuk';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    
    // Menyiapkan kolom untuk mencatat histori penambahan stok
    protected $allowedFields    = ['produk_id', 'qty', 'tanggal', 'keterangan'];

    protected $useTimestamps    = true;
}