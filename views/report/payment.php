<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datecontrol\DateControl;
use dosamigos\chartjs\ChartJs;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $startDate string */
/* @var $endDate string */
/* @var $totalRevenue float */
/* @var $revenueByMethod array */
/* @var $dailyRevenueData array */
/* @var $monthlyRevenueData array */
/* @var $paymentsProvider yii\data\ActiveDataProvider */

$this->title = 'Laporan Pembayaran';
$this->params['breadcrumbs'][] = ['label' => 'Laporan', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Prepare data for payment method chart
$methodLabels = [];
$methodData = [];
foreach ($revenueByMethod as $method) {
    $labels = [
        \app\models\Payment::METHOD_CASH => 'Tunai',
        \app\models\Payment::METHOD_CARD => 'Kartu Kredit',
    ];
    $methodLabels[] = $labels[$method['payment_method']] ?? $method['payment_method'];
    $methodData[] = (float)$method['total'];
}

?>
<div class="report-payment">
    <h1><?= Html::encode($this->title) ?></h1>
    
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Filter Periode</h3>
                </div>
                <div class="panel-body">
                    <?php $form = ActiveForm::begin(['method' => 'get', 'action' => ['payment'], 'options' => ['class' => 'form-inline']]); ?>
                    
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
                        <?= Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset', ['payment'], ['class' => 'btn btn-default']) ?>
                    </div>
                    
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h3 class="panel-title">Total Pendapatan</h3>
                </div>
                <div class="panel-body">
                    <h2 class="text-center">Rp. <?= Yii::$app->formatter->asDecimal($totalRevenue, 0) ?></h2>
                    <p class="text-center text-muted">Total pendapatan pada periode <?= Yii::$app->formatter->asDate($startDate) ?> hingga <?= Yii::$app->formatter->asDate($endDate) ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Pendapatan Berdasarkan Metode Pembayaran</h3>
                </div>
                <div class="panel-body">
                    <?= ChartJs::widget([
                        'type' => 'pie',
                        'data' => [
                            'labels' => $methodLabels,
                            'datasets' => [
                                [
                                    'data' => $methodData,
                                    'backgroundColor' => ["#3498db", "#e74c3c", "#f39c12", "#2ecc71"],
                                    'borderColor' => ["#2980b9", "#c0392b", "#d35400", "#27ae60"],
                                    'borderWidth' => 1,
                                ]
                            ]
                        ],
                        'clientOptions' => [
                            'responsive' => true,
                            'legend' => [
                                'position' => 'bottom',
                            ],
                            'tooltips' => [
                                'callbacks' => [
                                    'label' => new \yii\web\JsExpression('
                                        function(tooltipItem, data) {
                                            var value = data.datasets[0].data[tooltipItem.index];
                                            var label = data.labels[tooltipItem.index];
                                            return label + ": Rp " + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                                        }
                                    ')
                                ]
                            ]
                        ]
                    ]); ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Tren Pendapatan Harian (<?= Yii::$app->formatter->asDate($startDate) ?> - <?= Yii::$app->formatter->asDate($endDate) ?>)</h3>
                </div>
                <div class="panel-body">
                    <?= ChartJs::widget([
                        'type' => 'line',
                        'data' => [
                            'labels' => array_keys($dailyRevenueData),
                            'datasets' => [
                                [
                                    'label' => 'Pendapatan Harian (Rp)',
                                    'data' => array_values($dailyRevenueData),
                                    'backgroundColor' => "rgba(46, 204, 113, 0.2)",
                                    'borderColor' => "rgba(46, 204, 113, 1)",
                                    'pointBackgroundColor' => "rgba(46, 204, 113, 1)",
                                    'pointBorderColor' => "#fff",
                                    'pointHoverBackgroundColor' => "#fff",
                                    'pointHoverBorderColor' => "rgba(46, 204, 113, 1)",
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
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Tren Pendapatan Bulanan (12 Bulan Terakhir)</h3>
                </div>
                <div class="panel-body">
                    <?= ChartJs::widget([
                        'type' => 'bar',
                        'data' => [
                            'labels' => array_keys($monthlyRevenueData),
                            'datasets' => [
                                [
                                    'label' => 'Pendapatan Bulanan (Rp)',
                                    'data' => array_values($monthlyRevenueData),
                                    'backgroundColor' => "rgba(52, 152, 219, 0.6)",
                                    'borderColor' => "rgba(52, 152, 219, 1)",
                                    'borderWidth' => 1,
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
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Daftar Pembayaran (Periode: <?= Yii::$app->formatter->asDate($startDate) ?> - <?= Yii::$app->formatter->asDate($endDate) ?>)</h3>
                </div>
                <div class="panel-body">
                    <?= GridView::widget([
                        'dataProvider' => $paymentsProvider,
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
                            'receipt_number',
                            [
                                'attribute' => 'medical_record_id',
                                'value' => function ($model) {
                                    return $model->medicalRecord ? ($model->medicalRecord->patient ? $model->medicalRecord->patient->name : 'N/A') : 'N/A';
                                },
                                'label' => 'Pasien',
                            ],
                            [
                                'attribute' => 'payment_date',
                                'format' => 'datetime',
                                'label' => 'Tanggal Pembayaran',
                            ],
                            [
                                'attribute' => 'amount',
                                'value' => function ($model) {
                                    return 'Rp. ' . Yii::$app->formatter->asDecimal($model->amount, 0);
                                },
                                'label' => 'Jumlah',
                            ],
                            [
                                'attribute' => 'payment_method',
                                'value' => function ($model) {
                                    return $model->getPaymentMethodLabel();
                                },
                                'label' => 'Metode Pembayaran',
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{view} {print}',
                                'buttons' => [
                                    'view' => function ($url, $model, $key) {
                                        return Html::a('<i class="glyphicon glyphicon-eye-open"></i>', ['/payment/view', 'id' => $model->id], [
                                            'class' => 'btn btn-xs btn-primary',
                                            'title' => 'Lihat Detail',
                                            'data-pjax' => 0,
                                        ]);
                                    },
                                    'print' => function ($url, $model, $key) {
                                        return Html::a('<i class="glyphicon glyphicon-print"></i>', ['/payment/print', 'id' => $model->id], [
                                            'class' => 'btn btn-xs btn-success',
                                            'title' => 'Cetak Kuitansi',
                                            'target' => '_blank',
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
            <?= Html::a('<i class="glyphicon glyphicon-print"></i> Cetak Laporan', ['payment', 'start_date' => $startDate, 'end_date' => $endDate, 'format' => 'pdf'], [
                'class' => 'btn btn-success',
                'target' => '_blank'
            ]) ?>
            <?= Html::a('<i class="glyphicon glyphicon-download"></i> Export Excel', ['payment', 'start_date' => $startDate, 'end_date' => $endDate, 'format' => 'excel'], [
                'class' => 'btn btn-primary'
            ]) ?>
        </div>
    </div>
</div>
