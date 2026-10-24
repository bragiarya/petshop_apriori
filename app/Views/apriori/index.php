<?= $this->extend('layout/template') ?>
<?= $this->section('content') ?>

<style>
    .section-fade {
        animation: fadeIn 0.8s ease-in-out;
    }
    @keyframes fadeIn {
        0% { opacity: 0; transform: translateY(15px); }
        100% { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="row">
    <div class="col-12">
        
        <div class="card shadow-sm mb-4 border-primary section-fade">
            <div class="card-header bg-white fw-bold d-flex align-items-center">
                ⚙️ Pengaturan Nilai Threshold Apriori
            </div>
            <div class="card-body bg-light">
                <form action="<?= base_url('apriori') ?>" method="GET" class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Minimum Support</label>
                        <input type="number" step="0.01" min="0.01" max="1" name="support" class="form-control" value="<?= esc($support) ?>" required>
                        <small class="text-muted">Desimal. Contoh: 0.2 (20%)</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Minimum Confidence</label>
                        <input type="number" step="0.01" min="0.01" max="1" name="confidence" class="form-control" value="<?= esc($confidence) ?>" required>
                        <small class="text-muted">Desimal. Contoh: 0.5 (50%)</small>
                    </div>
                    <div class="col-md-4 mt-auto">
                        <button type="submit" class="btn btn-primary w-100 fw-bold">
                            🚀 Proses Analisis
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="alert alert-info section-fade mb-4">
            Total Data Dianalisis: <strong><?= $total_transaksi ?> Transaksi</strong>
        </div>

        <div class="card shadow-sm mb-4 section-fade" style="animation-delay: 0.2s; animation-fill-mode: both;">
            <div class="card-header bg-secondary text-white fw-bold">
                TAHAP 1 — Perhitungan Support Tiap Item (1-Itemset)
            </div>
            <div class="card-body">
                <p class="text-muted small">Menghitung kemunculan setiap item tunggal. Item yang memenuhi Minimum Support (>= <?= $support * 100 ?>%) akan lanjut ke Tahap 2.</p>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped datatable" width="100%">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Item</th>
                                <th>Jumlah Muncul (Count)</th>
                                <th>Support (%)</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach($tahap1 as $t1): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= esc($t1['item_name']) ?></td>
                                <td><?= $t1['count'] ?>x</td>
                                <td><?= $t1['support'] ?>%</td>
                                <td>
                                    <?php if($t1['is_lolos']): ?>
                                        <span class="badge bg-success">Lolos L1</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Tidak Lolos</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4 section-fade" style="animation-delay: 0.4s; animation-fill-mode: both;">
            <div class="card-header bg-secondary text-white fw-bold">
                TAHAP 2 — Frequent Itemset per Level (L1, L2, dst)
            </div>
            <div class="card-body">
                <?php if(empty($frequent_itemsets)): ?>
                    <div class="alert alert-danger">Tidak ada item yang memenuhi minimum support pada Tahap 1, proses berhenti di sini.</div>
                <?php else: ?>
                    
                    <?php foreach($frequent_itemsets as $level => $itemsets): ?>
                        <h6 class="fw-bold mt-3 text-primary border-bottom pb-2">Level L<?= $level ?> (Kombinasi <?= $level ?> Item)</h6>
                        
                        <?php if(empty($itemsets)): ?>
                            <div class="alert alert-warning py-2"><small>Tidak ada kombinasi item yang memenuhi minimum support di level ini, proses iterasi dihentikan.</small></div>
                        <?php else: ?>
                            <div class="table-responsive mb-4">
                                <table class="table table-bordered table-sm w-75">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="10%">No</th>
                                            <th width="50%">Itemset</th>
                                            <th width="20%">Support Count</th>
                                            <th width="20%">Support (%)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $noL = 1; foreach($itemsets as $set): ?>
                                        <tr>
                                            <td><?= $noL++ ?></td>
                                            <td class="fw-bold"><?= esc($set['item_name']) ?></td>
                                            <td><?= $set['count'] ?>x</td>
                                            <td class="text-success fw-bold"><?= $set['support'] ?>%</td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>


        <div class="card shadow-sm mb-5 section-fade" style="animation-delay: 0.8s; animation-fill-mode: both;">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">TAHAP 3 — Hasil Akhir (Association Rules)</h5>
                <a href="<?= base_url('apriori/export?support=' . esc($support) . '&confidence=' . esc($confidence)) ?>" class="btn btn-success btn-sm fw-bold shadow-sm">
                    📥 Export Excel
                </a>
            </div>
            <div class="card-body">
                <?php if(empty($rules)): ?>
                    <div class="alert alert-warning">Tidak ditemukan aturan asosiasi (rules) yang memenuhi nilai Minimum Confidence (>= <?= $confidence * 100 ?>%).</div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <strong>Cara Membaca:</strong> Jika pelanggan membeli <b>[Pemicu]</b>, maka sangat disarankan untuk menawarkan <b>[Rekomendasi]</b>.
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped datatable" width="100%">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Item Pemicu</th>
                                    <th>Item Rekomendasi</th>
                                    <th>Support</th>
                                    <th>Confidence</th>
                                    <th>Lift</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $noR = 1; foreach($rules as $r): ?>
                                <tr>
                                    <td><?= $noR++ ?></td>
                                    <td class="fw-bold text-primary"><?= esc($r['pemicu']) ?></td>
                                    <td class="fw-bold text-success"><?= esc($r['rekomendasi']) ?></td>
                                    <td><?= $r['support'] ?>%</td>
                                    <td>
                                        <span class="badge <?= $r['confidence'] >= ($confidence*100) ? 'bg-success' : 'bg-secondary' ?>">
                                            <?= $r['confidence'] ?>%
                                        </span>
                                    </td>
                                    <td><?= $r['lift'] ?></td>
                                    <td>
                                        <span class="badge <?= $r['keterangan'] == 'Kuat' ? 'bg-info text-dark' : 'bg-warning text-dark' ?>">
                                            <?= $r['keterangan'] ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        
        <?php if(!empty($rules)): ?>
        <div class="card shadow-sm mb-4 border-warning section-fade" style="animation-delay: 0.6s; animation-fill-mode: both;">
            <div class="card-header bg-warning text-dark fw-bold">
                📊 Grafik Kekuatan Rekomendasi (Confidence Level)
            </div>
            <div class="card-body">
                <canvas id="aprioriChart" height="100"></canvas>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    <?php if(!empty($rules)): ?>
        const dataApriori = <?= json_encode($rules) ?>;
        
        let labels = [];
        let confidences = [];

        dataApriori.forEach(item => {
            labels.push(item.pemicu + ' ➡️ ' + item.rekomendasi);
            confidences.push(item.confidence);
        });

        const ctx = document.getElementById('aprioriChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Tingkat Keyakinan / Confidence (%)',
                    data: confidences,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100 
                    }
                }
            }
        });
    <?php endif; ?>
</script>
<?= $this->endSection() ?>