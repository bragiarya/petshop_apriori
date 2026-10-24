<?php

namespace App\Libraries;

class AprioriAlgorithm
{
    protected $minSupport;
    protected $minConfidence;

    public function __construct($minSupport = 0.2, $minConfidence = 0.5)
    {
        $this->minSupport = $minSupport;
        $this->minConfidence = $minConfidence;
    }

    public function analyze($transactions)
    {
        $totalTransactions = count($transactions);
        if ($totalTransactions == 0) {
            return ['tahap1' => [], 'frequent_itemsets' => [], 'rules' => []];
        }

        // ==========================================
        // TAHAP 1: Hitung kemunculan tiap item (1-Itemset)
        // ==========================================
        $itemCounts = [];
        foreach ($transactions as $transaction) {
            foreach ($transaction as $item) {
                if (!isset($itemCounts[$item])) $itemCounts[$item] = 0;
                $itemCounts[$item]++;
            }
        }

        $tahap1 = [];
        $l1 = []; // Menyimpan item yang Lolos (L1)
        foreach ($itemCounts as $item => $count) {
            $support = $count / $totalTransactions;
            $isLolos = $support >= $this->minSupport;
            
            // Simpan rekam jejak untuk Tahap 1
            $tahap1[] = [
                'itemset'  => [$item],
                'count'    => $count,
                'support'  => $support,
                'is_lolos' => $isLolos
            ];

            if ($isLolos) {
                $l1[] = $item;
            }
        }

        $frequent_itemsets = [];
        
        // Simpan data L1 ke dalam Frequent Itemsets level 1
        $frequent_itemsets[1] = [];
        foreach ($l1 as $item) {
            $frequent_itemsets[1][] = [
                'itemset' => [$item],
                'count'   => $itemCounts[$item],
                'support' => $itemCounts[$item] / $totalTransactions
            ];
        }

        // ==========================================
        // TAHAP 2 & 3: Kombinasi 2 Item (L2) & Rules
        // ==========================================
        $l2 = [];
        $rules = [];
        $l1Count = count($l1);

        for ($i = 0; $i < $l1Count; $i++) {
            for ($j = $i + 1; $j < $l1Count; $j++) {
                $itemA = $l1[$i];
                $itemB = $l1[$j];

                $freqAB = 0; $freqA = 0; $freqB = 0;

                foreach ($transactions as $transaction) {
                    $hasA = in_array($itemA, $transaction);
                    $hasB = in_array($itemB, $transaction);
                    if ($hasA) $freqA++;
                    if ($hasB) $freqB++;
                    if ($hasA && $hasB) $freqAB++;
                }

                $supportAB = $freqAB / $totalTransactions;

                // Jika kombinasi A dan B lolos min support (Masuk L2)
                if ($supportAB >= $this->minSupport) {
                    $l2[] = [
                        'itemset' => [$itemA, $itemB],
                        'count'   => $freqAB,
                        'support' => $supportAB
                    ];

                    // Hitung Confidence dan Lift
                    $confAB = $freqA > 0 ? ($freqAB / $freqA) : 0;
                    $confBA = $freqB > 0 ? ($freqAB / $freqB) : 0;
                    $suppA = $freqA / $totalTransactions;
                    $suppB = $freqB / $totalTransactions;
                    $lift = ($suppA * $suppB) > 0 ? ($supportAB / ($suppA * $suppB)) : 0;

                    if ($confAB >= $this->minConfidence) {
                        $rules[] = [
                            'antecedent' => [$itemA], 'consequent' => [$itemB],
                            'support' => $supportAB, 'confidence' => $confAB, 'lift' => $lift
                        ];
                    }
                    if ($confBA >= $this->minConfidence) {
                        $rules[] = [
                            'antecedent' => [$itemB], 'consequent' => [$itemA],
                            'support' => $supportAB, 'confidence' => $confBA, 'lift' => $lift
                        ];
                    }
                }
            }
        }

        if (!empty($l2)) {
            $frequent_itemsets[2] = $l2;
        }

        // Urutkan Rules dari confidence terbesar
        usort($rules, function($a, $b) { return $b['confidence'] <=> $a['confidence']; });

        // Kode ini Mengembalikan 3 paket data sekaligus ke Controller
        return [
            'tahap1'            => $tahap1,
            'frequent_itemsets' => $frequent_itemsets,
            'rules'             => $rules
        ];
    }
}