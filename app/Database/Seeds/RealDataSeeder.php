<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RealDataSeeder extends Seeder
{
    public function run()
    {
        // 1. Array kompresi dari 151 baris Excel kamu (Format: ID_Transaksi | Tanggal | Item1,Item2)
        $mentah = [
            'T001|2026-01-01|Makanan Anjing,Tali Anjing,Vitamin Anjing', 'T002|2026-01-02|Makanan Kucing,Pasir Kucing,Shampo Hewan',
            'T003|2026-01-03|Makanan Kucing,Pasir Kucing,Shampo Hewan', 'T004|2026-01-04|Makanan Anjing,Vitamin Anjing,Shampo Hewan',
            'T005|2026-01-05|Makanan Ikan,Filter Air,Vitamin Ikan', 'T006|2026-01-06|Makanan Anjing,Tali Anjing,Vitamin Anjing',
            'T007|2026-01-07|Makanan Kucing,Mainan Kucing,Pasir Kucing', 'T008|2026-01-08|Makanan Anjing,Vitamin Anjing,Shampo Hewan',
            'T009|2026-01-09|Makanan Kucing,Mainan Kucing,Pasir Kucing', 'T010|2026-01-10|Makanan Kucing,Mainan Kucing,Vitamin Kucing',
            'T011|2026-01-11|Makanan Kucing,Pasir Kucing,Vitamin Kucing', 'T012|2026-01-12|Makanan Kucing,Pasir Kucing,Vitamin Kucing',
            'T013|2026-01-13|Makanan Kucing,Pasir Kucing,Vitamin Kucing', 'T014|2026-01-14|Makanan Burung,Sangkar,Vitamin Burung',
            'T015|2026-01-15|Makanan Kucing,Pasir Kucing,Shampo Hewan', 'T016|2026-01-16|Makanan Kucing,Mainan Kucing,Vitamin Kucing',
            'T017|2026-01-17|Makanan Anjing,Tali Anjing,Vitamin Anjing', 'T018|2026-01-18|Makanan Kucing,Pasir Kucing,Shampo Hewan',
            'T019|2026-01-19|Makanan Kucing,Pasir Kucing,Shampo Hewan', 'T020|2026-01-20|Makanan Kucing,Mainan Kucing,Vitamin Kucing',
            'T021|2026-01-21|Makanan Kucing,Mainan Kucing,Vitamin Kucing', 'T022|2026-01-22|Makanan Kucing,Mainan Kucing,Vitamin Kucing',
            'T023|2026-01-23|Makanan Kucing,Pasir Kucing,Vitamin Kucing', 'T024|2026-01-24|Makanan Kucing,Pasir Kucing,Shampo Hewan',
            'T025|2026-01-25|Makanan Kucing,Mainan Kucing,Vitamin Kucing', 'T026|2026-01-26|Makanan Burung,Sangkar,Vitamin Burung',
            'T027|2026-01-27|Makanan Anjing,Tali Anjing,Vitamin Anjing', 'T028|2026-01-28|Makanan Kucing,Pasir Kucing,Shampo Hewan',
            'T029|2026-01-29|Makanan Kucing,Pasir Kucing,Shampo Hewan', 'T030|2026-01-30|Makanan Kucing,Mainan Kucing,Pasir Kucing',
            'T031|2026-01-31|Makanan Anjing,Tali Anjing,Vitamin Anjing', 'T032|2026-02-01|Makanan Kucing,Mainan Kucing,Pasir Kucing',
            'T033|2026-02-02|Makanan Kucing,Pasir Kucing,Vitamin Kucing', 'T034|2026-02-03|Makanan Kucing,Pasir Kucing,Vitamin Kucing',
            'T035|2026-02-04|Makanan Kucing,Mainan Kucing,Vitamin Kucing', 'T036|2026-02-05|Makanan Kucing,Pasir Kucing,Shampo Hewan',
            'T037|2026-02-06|Makanan Kucing,Pasir Kucing,Shampo Hewan', 'T038|2026-02-07|Makanan Kucing,Pasir Kucing,Vitamin Kucing',
            'T039|2026-02-08|Makanan Kucing,Mainan Kucing,Vitamin Kucing', 'T040|2026-02-09|Makanan Kucing,Pasir Kucing,Vitamin Kucing',
            'T041|2026-02-10|Makanan Ikan,Filter Air,Vitamin Ikan', 'T042|2026-02-11|Makanan Kucing,Mainan Kucing,Vitamin Kucing',
            'T043|2026-02-12|Makanan Anjing,Vitamin Anjing,Shampo Hewan', 'T044|2026-02-13|Makanan Kucing,Mainan Kucing,Vitamin Kucing',
            'T045|2026-02-14|Makanan Kucing,Mainan Kucing,Vitamin Kucing', 'T046|2026-02-15|Makanan Ikan,Filter Air,Vitamin Ikan',
            'T047|2026-02-16|Makanan Burung,Sangkar,Vitamin Burung', 'T048|2026-02-17|Makanan Kucing,Mainan Kucing,Pasir Kucing',
            'T049|2026-02-18|Makanan Anjing,Vitamin Anjing,Shampo Hewan', 'T050|2026-02-19|Makanan Burung,Sangkar,Vitamin Burung',
            'T051|2026-02-20|Makanan Anjing,Vitamin Anjing,Shampo Hewan', 'T052|2026-02-21|Makanan Kucing,Mainan Kucing,Vitamin Kucing',
            'T053|2026-02-22|Makanan Ikan,Filter Air,Vitamin Ikan', 'T054|2026-02-23|Makanan Kucing,Pasir Kucing,Vitamin Kucing',
            'T055|2026-02-24|Makanan Ikan,Filter Air,Vitamin Ikan', 'T056|2026-01-01|Makanan Kucing,Mainan Kucing,Pasir Kucing',
            'T057|2026-02-26|Makanan Kucing,Pasir Kucing,Vitamin Kucing', 'T058|2026-02-27|Makanan Kucing,Pasir Kucing,Vitamin Kucing',
            'T059|2026-02-28|Makanan Kucing,Pasir Kucing,Vitamin Kucing', 'T060|2026-03-01|Makanan Kucing,Pasir Kucing,Shampo Hewan',
            'T061|2026-03-02|Makanan Ikan,Filter Air,Vitamin Ikan', 'T062|2026-03-03|Makanan Kucing,Pasir Kucing,Vitamin Kucing',
            'T063|2026-03-04|Makanan Kucing,Pasir Kucing,Shampo Hewan', 'T064|2026-03-05|Makanan Kucing,Pasir Kucing,Vitamin Kucing',
            'T065|2026-03-06|Makanan Anjing,Tali Anjing,Vitamin Anjing', 'T066|2026-03-07|Makanan Kucing,Mainan Kucing,Pasir Kucing',
            'T067|2026-03-08|Makanan Kucing,Mainan Kucing,Vitamin Kucing', 'T068|2026-03-09|Makanan Ikan,Filter Air,Vitamin Ikan',
            'T069|2026-03-10|Makanan Burung,Sangkar,Vitamin Burung', 'T070|2026-03-11|Makanan Anjing,Vitamin Anjing,Shampo Hewan',
            'T071|2026-03-12|Makanan Burung,Sangkar,Vitamin Burung', 'T072|2026-03-13|Makanan Kucing,Pasir Kucing,Vitamin Kucing',
            'T073|2026-03-14|Makanan Kucing,Mainan Kucing,Pasir Kucing', 'T074|2026-03-15|Makanan Kucing,Mainan Kucing,Vitamin Kucing',
            'T075|2026-03-16|Makanan Anjing,Tali Anjing,Vitamin Anjing', 'T076|2026-03-17|Makanan Kucing,Mainan Kucing,Pasir Kucing',
            'T077|2026-03-18|Makanan Kucing,Mainan Kucing,Vitamin Kucing', 'T078|2026-03-19|Makanan Ikan,Filter Air,Vitamin Ikan',
            'T079|2026-03-20|Makanan Kucing,Mainan Kucing,Pasir Kucing', 'T080|2026-03-21|Makanan Kucing,Pasir Kucing,Vitamin Kucing',
            'T081|2026-03-22|Makanan Ikan,Filter Air,Vitamin Ikan', 'T082|2026-03-23|Makanan Burung,Sangkar,Vitamin Burung',
            'T083|2026-03-24|Makanan Kucing,Pasir Kucing,Vitamin Kucing', 'T084|2026-03-25|Makanan Burung,Sangkar,Vitamin Burung',
            'T085|2026-03-26|Makanan Anjing,Vitamin Anjing,Shampo Hewan', 'T086|2026-03-27|Makanan Kucing,Mainan Kucing,Vitamin Kucing',
            'T087|2026-03-28|Makanan Ikan,Filter Air,Vitamin Ikan', 'T088|2026-03-29|Makanan Anjing,Tali Anjing,Vitamin Anjing',
            'T089|2026-03-30|Makanan Kucing,Mainan Kucing,Vitamin Kucing', 'T090|2026-03-31|Makanan Anjing,Tali Anjing,Vitamin Anjing',
            'T091|2026-04-01|Makanan Kucing,Mainan Kucing,Vitamin Kucing', 'T092|2026-04-02|Makanan Burung,Sangkar,Vitamin Burung',
            'T093|2026-04-03|Makanan Ikan,Filter Air,Vitamin Ikan', 'T094|2026-04-04|Makanan Kucing,Mainan Kucing,Vitamin Kucing',
            'T095|2026-04-05|Makanan Kucing,Pasir Kucing,Vitamin Kucing', 'T096|2026-04-06|Makanan Kucing,Mainan Kucing,Pasir Kucing',
            'T097|2026-04-07|Makanan Ikan,Filter Air,Vitamin Ikan', 'T098|2026-04-08|Makanan Kucing,Pasir Kucing,Vitamin Kucing',
            'T099|2026-04-09|Makanan Anjing,Tali Anjing,Vitamin Anjing', 'T100|2026-04-10|Makanan Kucing,Mainan Kucing,Pasir Kucing',
            'T101|2026-04-11|Makanan Anjing,Tali Anjing,Vitamin Anjing', 'T102|2026-04-12|Makanan Kucing,Pasir Kucing,Shampo Hewan',
            'T103|2026-04-13|Makanan Kucing,Mainan Kucing,Pasir Kucing', 'T104|2026-04-14|Makanan Kucing,Mainan Kucing,Pasir Kucing',
            'T105|2026-04-15|Makanan Burung,Sangkar,Vitamin Burung', 'T106|2026-04-16|Makanan Burung,Sangkar,Vitamin Burung',
            'T107|2026-04-17|Makanan Burung,Sangkar,Vitamin Burung', 'T108|2026-04-18|Makanan Kucing,Mainan Kucing,Pasir Kucing',
            'T109|2026-04-19|Makanan Anjing,Vitamin Anjing,Shampo Hewan', 'T110|2026-04-20|Makanan Anjing,Vitamin Anjing,Shampo Hewan',
            'T111|2026-04-21|Makanan Kucing,Pasir Kucing,Shampo Hewan', 'T112|2026-04-22|Makanan Ikan,Filter Air,Vitamin Ikan',
            'T113|2026-04-23|Makanan Kucing,Mainan Kucing,Pasir Kucing', 'T114|2026-04-24|Makanan Kucing,Pasir Kucing,Shampo Hewan',
            'T115|2026-04-25|Makanan Kucing,Mainan Kucing,Vitamin Kucing', 'T116|2026-04-26|Makanan Anjing,Vitamin Anjing,Shampo Hewan',
            'T117|2026-04-27|Makanan Kucing,Mainan Kucing,Pasir Kucing', 'T118|2026-04-28|Makanan Kucing,Mainan Kucing,Pasir Kucing',
            'T119|2026-04-29|Makanan Kucing,Mainan Kucing,Pasir Kucing', 'T120|2026-04-30|Makanan Kucing,Mainan Kucing,Pasir Kucing',
            'T121|2026-05-01|Makanan Anjing,Vitamin Anjing,Shampo Hewan', 'T122|2026-05-02|Makanan Ikan,Filter Air,Vitamin Ikan',
            'T123|2026-05-03|Makanan Kucing,Mainan Kucing,Vitamin Kucing', 'T124|2026-05-04|Makanan Burung,Sangkar,Vitamin Burung',
            'T125|2026-05-05|Makanan Anjing,Vitamin Anjing,Shampo Hewan', 'T126|2026-05-06|Makanan Anjing,Vitamin Anjing,Shampo Hewan',
            'T127|2026-05-07|Makanan Kucing,Pasir Kucing,Shampo Hewan', 'T128|2026-05-08|Makanan Kucing,Mainan Kucing,Vitamin Kucing',
            'T129|2026-05-09|Makanan Kucing,Mainan Kucing,Vitamin Kucing', 'T130|2026-05-10|Makanan Kucing,Pasir Kucing,Shampo Hewan',
            'T131|2026-05-11|Makanan Ikan,Filter Air,Vitamin Ikan', 'T132|2026-05-12|Makanan Burung,Sangkar,Vitamin Burung',
            'T133|2026-05-13|Makanan Kucing,Mainan Kucing,Pasir Kucing', 'T134|2026-05-14|Makanan Kucing,Mainan Kucing,Vitamin Kucing',
            'T135|2026-05-15|Makanan Ikan,Filter Air,Vitamin Ikan', 'T136|2026-05-16|Makanan Kucing,Mainan Kucing,Vitamin Kucing',
            'T137|2026-05-17|Makanan Kucing,Mainan Kucing,Vitamin Kucing', 'T138|2026-05-18|Makanan Kucing,Mainan Kucing,Pasir Kucing',
            'T139|2026-05-19|Makanan Kucing,Mainan Kucing,Pasir Kucing', 'T140|2026-05-20|Makanan Anjing,Tali Anjing,Vitamin Anjing',
            'T141|2026-05-21|Makanan Burung,Sangkar,Vitamin Burung', 'T142|2026-05-22|Makanan Kucing,Pasir Kucing,Shampo Hewan',
            'T143|2026-05-23|Makanan Burung,Sangkar,Vitamin Burung', 'T144|2026-05-24|Makanan Ikan,Filter Air,Vitamin Ikan',
            'T145|2026-05-25|Makanan Anjing,Tali Anjing,Vitamin Anjing', 'T146|2026-05-26|Makanan Kucing,Pasir Kucing,Shampo Hewan',
            'T147|2026-05-27|Makanan Kucing,Pasir Kucing,Shampo Hewan', 'T148|2026-05-28|Makanan Kucing,Pasir Kucing,Vitamin Kucing',
            'T149|2026-05-29|Makanan Anjing,Vitamin Anjing,Shampo Hewan', 'T150|2026-05-30|Makanan Kucing,Mainan Kucing,Pasir Kucing',
            'T151|2026-01-01|Makanan Kucing,Pasir Kucing,Shampo Hewan'
        ];

        // 2. Kamus Produk & Harga (karena di Excel tidak ada harganya)
        $katalog = [
            'Filter Air' => 50000, 'Mainan Anjing' => 25000, 'Mainan Kucing' => 20000, 
            'Makanan Anjing' => 80000, 'Makanan Burung' => 15000, 'Makanan Ikan' => 10000, 
            'Makanan Kucing' => 65000, 'Pasir Kucing' => 35000, 'Sangkar' => 150000, 
            'Shampo Hewan' => 30000, 'Tali Anjing' => 45000, 'Vitamin Anjing' => 40000, 
            'Vitamin Burung' => 12000, 'Vitamin Ikan' => 10000, 'Vitamin Kucing' => 35000
        ];

        // Eksekusi Logika: Mengolah teks array menjadi baris database
        $produkInsert = [];
        $idProduk = 1;
        $mapIdProduk = [];
        
        foreach ($katalog as $nama => $harga) {
            $produkInsert[] = ['id' => $idProduk, 'nama_produk' => $nama, 'harga' => $harga, 'stok' => 100, 'created_at' => date('Y-m-d H:i:s')];
            $mapIdProduk[$nama] = $idProduk;
            $idProduk++;
        }
        $this->db->table('produk')->insertBatch($produkInsert);

        $transaksiInsert = [];
        $detailInsert = [];
        $idTrans = 1;

        foreach ($mentah as $baris) {
            $data = explode('|', $baris);
            $no_invoice = $data[0];
            $tanggal = $data[1];
            $items = explode(',', $data[2]);
            
            $totalHarga = 0;
            foreach ($items as $item) {
                $item = trim($item);
                $totalHarga += $katalog[$item];
                $detailInsert[] = [
                    'transaksi_id' => $idTrans,
                    'produk_id'    => $mapIdProduk[$item],
                    'qty'          => 1,
                    'harga_satuan' => $katalog[$item],
                    'created_at'   => date('Y-m-d H:i:s')
                ];
            }
            
            $transaksiInsert[] = [
                'id' => $idTrans, 'no_invoice' => $no_invoice, 'tanggal' => $tanggal, 
                'total_harga' => $totalHarga, 'created_at' => date('Y-m-d H:i:s')
            ];
            $idTrans++;
        }

        $this->db->table('transaksi')->insertBatch($transaksiInsert);
        $this->db->table('detail_transaksi')->insertBatch($detailInsert);
    }
}