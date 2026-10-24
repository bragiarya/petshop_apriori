<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class StokKeluar extends Migration
{
    public function up()
    {
        // Membuat tabel catatan histori stok berkurang (bisa karena rusak, kadaluarsa, atau sinkronasi penjualan)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'produk_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'       => true,
            ],
            'qty' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'tanggal' => [
                'type' => 'DATE',
            ],
            'keterangan' => [
                'type' => 'TEXT', // Penjelasan: Misalnya "Kemasan rusak digigit tikus"
                'null' => true,
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
        
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('produk_id', 'produk', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('stok_keluar');
    }

    public function down()
    {
        $this->forge->dropTable('stok_keluar');
    }
}