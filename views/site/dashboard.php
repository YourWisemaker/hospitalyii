<?php

use yii\helpers\Html;
use yii\helpers\Url;
use dosamigos\chartjs\ChartJs;

/* @var $this yii\web\View */
/* @var $totalPatients int */
/* @var $totalMedicines int */
/* @var $totalTreatments int */
/* @var $todayAppointments int */
/* @var $pendingPayments int */
/* @var $totalIncome float */
/* @var $recentMedicalRecords array */
/* @var $latestPatients array */
/* @var $lowStockMedicines array */
/* @var $dailyPatientData array */
/* @var $revenueData array */

$this->title = 'Dashboard';
?>
<div class="site-dashboard">
    <div class="row">
        <div class="col-md-3">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Total Pasien</h3>
                </div>
                <div class="panel-body">
                    <h2 class="text-center"><?= $totalPatients ?></h2>
                    <p class="text-center">
                        <a href="<?= Url::to(['/patient/index']) ?>" class="btn btn-default btn-sm">
                            <i class="glyphicon glyphicon-list"></i> Lihat Semua
                        </a>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h3 class="panel-title">Total Karyawan</h3>
                </div>
                <div class="panel-body">
                    <h2 class="text-center"><?= $totalMedicines ?></h2>
                    <p class="text-center">
                        <a href="<?= Url::to(['/employee/index']) ?>" class="btn btn-default btn-sm">
                            <i class="glyphicon glyphicon-list"></i> Lihat Semua
                        </a>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">Rekam Medis</h3>
                </div>
                <div class="panel-body">
                    <h2 class="text-center"><?= $totalTreatments ?></h2>
                    <p class="text-center">
                        <a href="<?= Url::to(['/medical-record/index']) ?>" class="btn btn-default btn-sm">
                            <i class="glyphicon glyphicon-list"></i> Lihat Semua
                        </a>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="panel panel-warning">
                <div class="panel-heading">
                    <h3 class="panel-title">Total Pendapatan</h3>
                </div>
                <div class="panel-body">
                    <h2 class="text-center">Rp. <?= Yii::$app->formatter->asDecimal($totalIncome, 0) ?></h2>
                    <p class="text-center">
                        <a href="<?= Url::to(['/payment/report']) ?>" class="btn btn-default btn-sm">
                            <i class="glyphicon glyphicon-stats"></i> Lihat Laporan
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Pasien Perhari (30 Hari Terakhir)</h3>
                </div>
                <div class="panel-body">
                    <?= ChartJs::widget([
                        'type' => 'line',
                        'options' => [
                            'height' => 280,
                        ],
                        'data' => [
                            'labels' => array_keys($dailyPatientData),
                            'datasets' => [
                                [
                                    'label' => 'Jumlah Pasien',
                                    'backgroundColor' => "rgba(66, 139, 202, 0.2)",
                                    'borderColor' => "rgba(66, 139, 202, 1)",
                                    'pointBackgroundColor' => "rgba(66, 139, 202, 1)",
                                    'pointBorderColor' => "#fff",
                                    'pointHoverBackgroundColor' => "#fff",
                                    'pointHoverBorderColor' => "rgba(66, 139, 202, 1)",
                                    'data' => array_values($dailyPatientData)
                                ]
                            ]
                        ],
                        'clientOptions' => [
                            'responsive' => true,
                            'scales' => [
                                'yAxes' => [
                                    [
                                        'ticks' => [
                                            'beginAtZero' => true,
                                            'stepSize' => 1
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]); ?>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Menu Cepat</h3>
                </div>
                <div class="panel-body">
                    <div class="list-group">
                        <?= Html::a('<i class="glyphicon glyphicon-plus"></i> Tambah Pasien Baru', ['/patient/create'], ['class' => 'list-group-item']) ?>
                        <?= Html::a('<i class="glyphicon glyphicon-plus"></i> Buat Rekam Medis Baru', ['/medical-record/create'], ['class' => 'list-group-item']) ?>
                        <?= Html::a('<i class="glyphicon glyphicon-credit-card"></i> Proses Pembayaran', ['/payment/index'], ['class' => 'list-group-item']) ?>
                        <?= Html::a('<i class="glyphicon glyphicon-list"></i> Kelola Obat', ['/medicine/index'], ['class' => 'list-group-item']) ?>
                        <?= Html::a('<i class="glyphicon glyphicon-list"></i> Kelola Tindakan', ['/treatment/index'], ['class' => 'list-group-item']) ?>
                        <?= Html::a('<i class="glyphicon glyphicon-user"></i> Kelola Karyawan', ['/employee/index'], ['class' => 'list-group-item']) ?>
                    </div>
                </div>
            </div>
            
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Pendapatan Bulanan</h3>
                </div>
                <div class="panel-body">
                    <?= ChartJs::widget([
                        'type' => 'bar',
                        'options' => [
                            'height' => 200,
                        ],
                        'data' => [
                            'labels' => array_keys($revenueData),
                            'datasets' => [
                                [
                                    'label' => 'Pendapatan (Rp)',
                                    'backgroundColor' => "rgba(92, 184, 92, 0.8)",
                                    'borderColor' => "rgba(92, 184, 92, 1)",
                                    'pointBackgroundColor' => "rgba(92, 184, 92, 1)",
                                    'pointBorderColor' => "#fff",
                                    'pointHoverBackgroundColor' => "#fff",
                                    'pointHoverBorderColor' => "rgba(92, 184, 92, 1)",
                                    'data' => array_values($revenueData)
                                ]
                            ]
                        ],
                        'clientOptions' => [
                            'responsive' => true,
                            'scales' => [
                                'yAxes' => [
                                    [
                                        'ticks' => [
                                            'beginAtZero' => true,
                                            'callback' => new \yii\web\JsExpression('
                                                function(value, index, values) {
                                                    return "Rp " + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                                                }
                                            ')
                                        ]
                                    ]
                                ]
                            ],
                            'tooltips' => [
                                'callbacks' => [
                                    'label' => new \yii\web\JsExpression('
                                        function(tooltipItem, data) {
                                            return data.datasets[tooltipItem.datasetIndex].label + ": Rp " + 
                                                tooltipItem.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                                        }
                                    ')
                                ]
                            ]
                        ]
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Pasien Terbaru</h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Nomor Pasien</th>
                                    <th>Nama</th>
                                    <th>Tanggal Pendaftaran</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($latestPatients)): ?>
                                    <?php foreach ($latestPatients as $patient): ?>
                                    <tr>
                                        <td><?= $patient->registration_number ?></td>
                                        <td><?= $patient->name ?></td>
                                        <td><?= Yii::$app->formatter->asDate($patient->created_at) ?></td>
                                        <td>
                                            <?= Html::a('<i class="glyphicon glyphicon-eye-open"></i>', ['/patient/view', 'id' => $patient->id], [
                                                'class' => 'btn btn-xs btn-default',
                                                'title' => 'Lihat'
                                            ]) ?>
                                            <?= Html::a('<i class="glyphicon glyphicon-plus"></i>', ['/medical-record/create', 'patient_id' => $patient->id], [
                                                'class' => 'btn btn-xs btn-success',
                                                'title' => 'Buat Rekam Medis'
                                            ]) ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">Tidak ada data pasien terbaru</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?= Html::a('Lihat Semua Pasien', ['/patient/index'], ['class' => 'btn btn-default btn-sm']) ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Rekam Medis Terbaru</h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Nomor</th>
                                    <th>Pasien</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recentMedicalRecords)): ?>
                                    <?php foreach ($recentMedicalRecords as $record): ?>
                                    <tr>
                                        <td><?= $record->id ?></td>
                                        <td><?= $record->patient ? $record->patient->name : '-' ?></td>
                                        <td><?= Yii::$app->formatter->asDate($record->treatment_date) ?></td>
                                        <td>
                                            <span class="label <?= $record->payment_status == \app\models\MedicalRecord::PAYMENT_STATUS_PAID ? 'label-success' : 'label-warning' ?>">
                                                <?= $record->getPaymentStatusLabel() ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?= Html::a('<i class="glyphicon glyphicon-eye-open"></i>', ['/medical-record/view', 'id' => $record->id], [
                                                'class' => 'btn btn-xs btn-default',
                                                'title' => 'Lihat'
                                            ]) ?>
                                            <?= Html::a('<i class="glyphicon glyphicon-credit-card"></i>', ['/medical-record/payment', 'id' => $record->id], [
                                                'class' => 'btn btn-xs btn-warning',
                                                'title' => 'Pembayaran'
                                            ]) ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada data rekam medis terbaru</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?= Html::a('Lihat Semua Rekam Medis', ['/medical-record/index'], ['class' => 'btn btn-default btn-sm']) ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-danger">
                <div class="panel-heading">
                    <h3 class="panel-title">Peringatan Stok Obat Menipis</h3>
                </div>
                <div class="panel-body">
                    <?php if (!empty($lowStockMedicines)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama Obat</th>
                                    <th>Sisa Stok</th>
                                    <th>Unit</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lowStockMedicines as $medicine): ?>
                                <tr>
                                    <td><?= $medicine->code ?></td>
                                    <td><?= $medicine->name ?></td>
                                    <td><strong><?= $medicine->stock ?></strong></td>
                                    <td><?= $medicine->unit ?></td>
                                    <td>
                                        <?= Html::a('<i class="glyphicon glyphicon-plus"></i> Tambah Stok', ['/medicine/update-stock', 'id' => $medicine->id], [
                                            'class' => 'btn btn-xs btn-primary',
                                        ]) ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <p class="text-center">Tidak ada obat dengan stok menipis.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
