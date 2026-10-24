<?php

namespace App\Models;

use CodeIgniter\Model;

class StokKeluarModel extends Model
{
    protected $table            = 'stok_keluar';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    
    // Menyiapkan kolom untuk mencatat histori pengurangan stok (misal karena rusak/kadaluarsa)
    protected $allowedFields    = ['produk_id', 'qty', 'tanggal', 'keterangan'];

    protected $useTimestamps    = true;
}