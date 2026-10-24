<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class StokMasuk extends Migration
{
    public function up()
    {
        // Membuat tabel catatan histori saat kamu menambahkan stok dari supplier
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
                'type' => 'TEXT', // Penjelasan: Misalnya "Restock dari Supplier A"
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
        $this->forge->createTable('stok_masuk');
    }

    public function down()
    {
        $this->forge->dropTable('stok_masuk');
    }
}