<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Produk extends Migration
{
    public function up()
    {
        // Kode ini Mendefinisikan kolom-kolom untuk tabel 'produk'
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true, // Unsigned berarti id tidak boleh bernilai minus
                'auto_increment' => true, // ID otomatis bertambah
            ],
            'nama_produk' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'harga' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'stok' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0, // Jika produk baru ditambah, stok defaultnya 0
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true); // Jadikan kolom 'id' sebagai Primary Key
        $this->forge->createTable('produk'); // Eksekusi pembuatan tabel
    }

    public function down()
    {
        // Kode ini Menghapus tabel produk jika kita membatalkan (rollback) migrasi
        $this->forge->dropTable('produk');
    }
}