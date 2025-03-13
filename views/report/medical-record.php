<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datecontrol\DateControl;
use dosamigos\chartjs\ChartJs;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $startDate string */
/* @var $endDate string */
/* @var $totalRecords int */
/* @var $periodRecords int */
/* @var $recordsByStatus array */
/* @var $recordsByPaymentStatus array */
/* @var $dailyRecordsData array */
/* @var $recordsProvider yii\data\ActiveDataProvider */

$this->title = 'Laporan Rekam Medis';
$this->params['breadcrumbs'][] = ['label' => 'Laporan', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Prepare data for status chart
$statusLabels = [];
$statusData = [];
foreach ($recordsByStatus as $status) {
    $labels = [
        \app\models\MedicalRecord::STATUS_ONGOING => 'Dalam Proses',
        \app\models\MedicalRecord::STATUS_COMPLETED => 'Selesai',
    ];
    $statusLabels[] = $labels[$status['status']] ?? $status['status'];
    $statusData[] = (int)$status['count'];
}

// Prepare data for payment status chart
$paymentStatusLabels = [];
$paymentStatusData = [];
foreach ($recordsByPaymentStatus as $status) {
    $labels = [
        \app\models\MedicalRecord::PAYMENT_STATUS_UNPAID => 'Belum Dibayar',
        \app\models\MedicalRecord::PAYMENT_STATUS_PARTIAL => 'Dibayar Sebagian',
        \app\models\MedicalRecord::PAYMENT_STATUS_PAID => 'Lunas',
    ];
    $paymentStatusLabels[] = $labels[$status['payment_status']] ?? $status['payment_status'];
    $paymentStatusData[] = (int)$status['count'];
}

?>
<div class="report-medical-record">
    <h1><?= Html::encode($this->title) ?></h1>
    
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Filter Periode</h3>
                </div>
                <div class="panel-body">
                    <?php $form = ActiveForm::begin(['method' => 'get', 'action' => ['medical-record'], 'options' => ['class' => 'form-inline']]); ?>
                    
                    <div class="form-group">
                        <label class="control-label">Tanggal Mulai</label>
                        <?= DateControl::widget([
                            'name' => 'start_date',
                            'value' => $startDate,
                            'type' => DateControl::FORMAT_DATE,
                            'widgetOptions' => [
                                'pluginOptions' => [
                                    'autoclose' => true,
                                    'format' => 'yyyy-mm-dd'
                                ]
                            ]
                        ]); ?>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label">Tanggal Akhir</label>
                        <?= DateControl::widget([
                            'name' => 'end_date',
                            'value' => $endDate,
                            'type' => DateControl::FORMAT_DATE,
                            'widgetOptions' => [
                                'pluginOptions' => [
                                    'autoclose' => true,
                                    'format' => 'yyyy-mm-dd'
                                ]
                            ]
                        ]); ?>
                    </div>
                    
                    <div class="form-group">
                        <?= Html::submitButton('<i class="glyphicon glyphicon-search"></i> Filter', ['class' => 'btn btn-primary']) ?>
                        <?= Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset', ['medical-record'], ['class' => 'btn btn-default']) ?>
                    </div>
                    
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Total Rekam Medis</h3>
                </div>
                <div class="panel-body">
                    <h2 class="text-center"><?= $totalRecords ?></h2>
                    <p class="text-center text-muted">Total keseluruhan rekam medis</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h3 class="panel-title">Rekam Medis Periode</h3>
                </div>
                <div class="panel-body">
                    <h2 class="text-center"><?= $periodRecords ?></h2>
                    <p class="text-center text-muted">Rekam medis pada periode <?= Yii::$app->formatter->asDate($startDate) ?> hingga <?= Yii::$app->formatter->asDate($endDate) ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Rekam Medis Berdasarkan Status</h3>
                </div>
                <div class="panel-body">
                    <?= ChartJs::widget([
                        'type' => 'pie',
                        'data' => [
                            'labels' => $statusLabels,
                            'datasets' => [
                                [
                                    'data' => $statusData,
                                    'backgroundColor' => ["#3498db", "#e74c3c"],
                                    'borderColor' => ["#2980b9", "#c0392b"],
                                    'borderWidth' => 1,
                                ]
                            ]
                        ],
                        'clientOptions' => [
                            'responsive' => true,
                            'legend' => [
                                'position' => 'bottom',
                            ]
                        ]
                    ]); ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Rekam Medis Berdasarkan Status Pembayaran</h3>
                </div>
                <div class="panel-body">
                    <?= ChartJs::widget([
                        'type' => 'pie',
                        'data' => [
                            'labels' => $paymentStatusLabels,
                            'datasets' => [
                                [
                                    'data' => $paymentStatusData,
                                    'backgroundColor' => ["#e74c3c", "#f39c12", "#2ecc71"],
                                    'borderColor' => ["#c0392b", "#d35400", "#27ae60"],
                                    'borderWidth' => 1,
                                ]
                            ]
                        ],
                        'clientOptions' => [
                            'responsive' => true,
                            'legend' => [
                                'position' => 'bottom',
                            ]
                        ]
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Tren Rekam Medis Harian (<?= Yii::$app->formatter->asDate($startDate) ?> - <?= Yii::$app->formatter->asDate($endDate) ?>)</h3>
                </div>
                <div class="panel-body">
                    <?= ChartJs::widget([
                        'type' => 'line',
                        'data' => [
                            'labels' => array_keys($dailyRecordsData),
                            'datasets' => [
                                [
                                    'label' => 'Jumlah Rekam Medis',
                                    'data' => array_values($dailyRecordsData),
                                    'backgroundColor' => "rgba(52, 152, 219, 0.2)",
                                    'borderColor' => "rgba(52, 152, 219, 1)",
                                    'pointBackgroundColor' => "rgba(52, 152, 219, 1)",
                                    'pointBorderColor' => "#fff",
                                    'pointHoverBackgroundColor' => "#fff",
                                    'pointHoverBorderColor' => "rgba(52, 152, 219, 1)",
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
                            ],
                        ]
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Daftar Rekam Medis (Periode: <?= Yii::$app->formatter->asDate($startDate) ?> - <?= Yii::$app->formatter->asDate($endDate) ?>)</h3>
                </div>
                <div class="panel-body">
                    <?= GridView::widget([
                        'dataProvider' => $recordsProvider,
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
                            'id',
                            [
                                'attribute' => 'patient_id',
                                'value' => 'patient.name',
                                'label' => 'Pasien',
                            ],
                            [
                                'attribute' => 'treatment_date',
                                'format' => 'datetime',
                                'label' => 'Tanggal Pemeriksaan',
                            ],
                            [
                                'attribute' => 'status',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    $statusClass = $model->status === \app\models\MedicalRecord::STATUS_COMPLETED ? 'label-success' : 'label-primary';
                                    return '<span class="label ' . $statusClass . '">' . $model->getStatusLabel() . '</span>';
                                },
                            ],
                            [
                                'attribute' => 'payment_status',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    $statusClass = '';
                                    if ($model->payment_status === \app\models\MedicalRecord::PAYMENT_STATUS_PAID) {
                                        $statusClass = 'label-success';
                                    } elseif ($model->payment_status === \app\models\MedicalRecord::PAYMENT_STATUS_PARTIAL) {
                                        $statusClass = 'label-warning';
                                    } else {
                                        $statusClass = 'label-danger';
                                    }
                                    return '<span class="label ' . $statusClass . '">' . $model->getPaymentStatusLabel() . '</span>';
                                },
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{view}',
                                'buttons' => [
                                    'view' => function ($url, $model, $key) {
                                        return Html::a('<i class="glyphicon glyphicon-eye-open"></i>', ['/medical-record/view', 'id' => $model->id], [
                                            'class' => 'btn btn-xs btn-primary',
                                            'title' => 'Lihat Detail',
                                            'data-pjax' => 0,
                                        ]);
                                    },
                                ],
                            ],
                        ],
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12 text-right">
            <?= Html::a('<i class="glyphicon glyphicon-print"></i> Cetak Laporan', ['medical-record', 'start_date' => $startDate, 'end_date' => $endDate, 'format' => 'pdf'], [
                'class' => 'btn btn-success',
                'target' => '_blank'
            ]) ?>
            <?= Html::a('<i class="glyphicon glyphicon-download"></i> Export Excel', ['medical-record', 'start_date' => $startDate, 'end_date' => $endDate, 'format' => 'excel'], [
                'class' => 'btn btn-primary'
            ]) ?>
        </div>
    </div>
</div>
