<?php

namespace App\Controllers;

use App\Models\DetailTransaksiModel;
use App\Models\ProdukModel;
use App\Libraries\AprioriAlgorithm;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AprioriController extends BaseController
{
    public function index()
    {
        $inputSupport    = $this->request->getGet('support') ?? 0.2;
        $inputConfidence = $this->request->getGet('confidence') ?? 0.5;

        $detailModel = new DetailTransaksiModel();
        $semuaTransaksi = $detailModel->findAll();

        $formatTransaksi = [];
        foreach ($semuaTransaksi as $row) {
            $formatTransaksi[$row['transaksi_id']][] = $row['produk_id'];
        }
        $formatTransaksi = array_values($formatTransaksi);

        $apriori = new AprioriAlgorithm((float)$inputSupport, (float)$inputConfidence);
        
        // PENTING: Sekarang hasil_apriori berisi 3 array: tahap1, frequent_itemsets, rules
        $hasil_apriori = $apriori->analyze($formatTransaksi);

        $produkModel = new ProdukModel();
        $semuaProduk = $produkModel->findAll();
        $mapProduk = [];
        foreach ($semuaProduk as $p) {
            $mapProduk[$p['id']] = $p['nama_produk'];
        }

        // Fungsi bantuan (Helper) untuk mengubah array ID menjadi string Nama Produk
        $ubahKeNama = function($itemset) use ($mapProduk) {
            $namaArray = array_map(function($id) use ($mapProduk) {
                return $mapProduk[$id] ?? 'Unknown';
            }, $itemset);
            return implode(' + ', $namaArray);
        };

        // 1. Format Data Tahap 1
        $dataTahap1 = [];
        foreach ($hasil_apriori['tahap1'] as $t1) {
            $t1['item_name'] = $ubahKeNama($t1['itemset']);
            $t1['support']   = round($t1['support'] * 100, 2);
            $dataTahap1[] = $t1;
        }

        // 2. Format Data Tahap 2 (Frequent Itemsets per Level)
        $dataFrequent = [];
        foreach ($hasil_apriori['frequent_itemsets'] as $level => $itemsets) {
            $dataFrequent[$level] = [];
            foreach ($itemsets as $set) {
                $set['item_name'] = $ubahKeNama($set['itemset']);
                $set['support']   = round($set['support'] * 100, 2);
                $dataFrequent[$level][] = $set;
            }
        }

        // 3. Format Data Tahap 3 (Rules)
        $dataRules = [];
        foreach ($hasil_apriori['rules'] as $rule) {
            $lift = round($rule['lift'], 2);
            $dataRules[] = [
                'pemicu'      => $ubahKeNama($rule['antecedent']),
                'rekomendasi' => $ubahKeNama($rule['consequent']),
                'support'     => round($rule['support'] * 100, 2),
                'confidence'  => round($rule['confidence'] * 100, 2),
                'lift'        => $lift,
                'keterangan'  => $lift > 1 ? 'Kuat' : 'Lemah' // Logika Keterangan Baru
            ];
        }

        $data = [
            'title'             => 'Analisis Asosiasi Apriori',
            'support'           => $inputSupport,
            'confidence'        => $inputConfidence,
            'total_transaksi'   => count($formatTransaksi),
            'tahap1'            => $dataTahap1,
            'frequent_itemsets' => $dataFrequent,
            'rules'             => $dataRules
        ];

        return view('apriori/index', $data);
    }

    public function exportExcel()
    {
        $inputSupport    = $this->request->getGet('support') ?? 0.2;
        $inputConfidence = $this->request->getGet('confidence') ?? 0.5;

        $detailModel = new DetailTransaksiModel();
        $semuaTransaksi = $detailModel->findAll();

        $formatTransaksi = [];
        foreach ($semuaTransaksi as $row) {
            $formatTransaksi[$row['transaksi_id']][] = $row['produk_id'];
        }
        $formatTransaksi = array_values($formatTransaksi);

        $apriori = new AprioriAlgorithm((float)$inputSupport, (float)$inputConfidence);
        $hasil_apriori = $apriori->analyze($formatTransaksi);
        $aturanAsosiasi = $hasil_apriori['rules']; // Ambil bagian rules saja untuk Excel

        $produkModel = new ProdukModel();
        $semuaProduk = $produkModel->findAll();
        $mapProduk = [];
        foreach ($semuaProduk as $p) {
            $mapProduk[$p['id']] = $p['nama_produk'];
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Tambahkan Header Keterangan di Excel
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Item Pemicu');
        $sheet->setCellValue('C1', 'Item Rekomendasi');
        $sheet->setCellValue('D1', 'Support (%)');
        $sheet->setCellValue('E1', 'Confidence (%)');
        $sheet->setCellValue('F1', 'Lift');
        $sheet->setCellValue('G1', 'Keterangan');
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);

        $baris = 2; $no = 1;
        foreach ($aturanAsosiasi as $aturan) {
            $lift = round($aturan['lift'], 2);
            $sheet->setCellValue('A' . $baris, $no++);
            $sheet->setCellValue('B' . $baris, $mapProduk[$aturan['antecedent'][0]] ?? 'Unknown');
            $sheet->setCellValue('C' . $baris, $mapProduk[$aturan['consequent'][0]] ?? 'Unknown');
            $sheet->setCellValue('D' . $baris, round($aturan['support'] * 100, 2));
            $sheet->setCellValue('E' . $baris, round($aturan['confidence'] * 100, 2));
            $sheet->setCellValue('F' . $baris, $lift);
            $sheet->setCellValue('G' . $baris, $lift > 1 ? 'Kuat' : 'Lemah');
            $baris++;
        }

        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Hasil_Analisis_Apriori.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit();
    }
}