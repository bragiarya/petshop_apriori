<?php

namespace App\Models;

use CodeIgniter\Model;

class TransaksiModel extends Model
{
    protected $table            = 'transaksi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    
    // Hanya kolom ini yang boleh diisi saat menyimpan data invoice baru
    protected $allowedFields    = ['no_invoice', 'tanggal', 'total_harga'];

    protected $useTimestamps    = true;
}