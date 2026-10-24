<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdukModel extends Model
{
    // Menghubungkan model ini dengan tabel 'produk' di database
    protected $table            = 'produk';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array'; // Kita pakai format array agar lebih mudah diolah nanti
    
    // Mendaftarkan kolom apa saja yang dizinkan untuk diisi/diubah lewat aplikasi (keamanan dari mass-assignment)
    protected $allowedFields    = ['nama_produk', 'harga', 'stok'];

    // Mengaktifkan fitur otomatis pengisian kolom 'created_at' dan 'updated_at' setiap ada proses insert/update
    protected $useTimestamps    = true;
}