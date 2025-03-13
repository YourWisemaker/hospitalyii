<?php

use yii\helpers\Html;
use yii\grid\GridView;
use dosamigos\chartjs\ChartJs;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $totalAmount float */
/* @var $dailyData array */
/* @var $paymentMethodData array */
/* @var $startDate string */
/* @var $endDate string */

$this->title = 'Laporan Pembayaran';
$this->params['breadcrumbs'][] = ['label' => 'Daftar Pembayaran', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-report">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Filter Laporan</h3>
        </div>
        <div class="panel-body">
            <form method="get" class="form-horizontal">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label col-sm-4">Dari Tanggal</label>
                            <div class="col-sm-8">
                                <input type="date" name="start_date" class="form-control" 
                                    value="<?= $startDate ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label col-sm-4">Sampai Tanggal</label>
                            <div class="col-sm-8">
                                <input type="date" name="end_date" class="form-control"
                                    value="<?= $endDate ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="glyphicon glyphicon-search"></i> Filter
                                </button>
                                <a href="<?= \yii\helpers\Url::to(['report']) ?>" class="btn btn-default">
                                    <i class="glyphicon glyphicon-refresh"></i> Reset
                                </a>
                                <button type="button" class="btn btn-success" onclick="window.print()">
                                    <i class="glyphicon glyphicon-print"></i> Cetak
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Total Pembayaran</h3>
                </div>
                <div class="panel-body">
                    <h2 class="text-center">Rp. <?= Yii::$app->formatter->asDecimal($totalAmount, 0) ?></h2>
                    <p class="text-center">
                        Periode: <?= Yii::$app->formatter->asDate($startDate) ?> s/d <?= Yii::$app->formatter->asDate($endDate) ?>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">Pembayaran Harian</h3>
                </div>
                <div class="panel-body">
                    <?= ChartJs::widget([
                        'type' => 'line',
                        'options' => [
                            'height' => 200,
                            'width' => 600
                        ],
                        'data' => [
                            'labels' => array_keys($dailyData),
                            'datasets' => [
                                [
                                    'label' => "Total Pembayaran (Rp)",
                                    'backgroundColor' => "rgba(66, 139, 202, 0.2)",
                                    'borderColor' => "rgba(66, 139, 202, 1)",
                                    'pointBackgroundColor' => "rgba(66, 139, 202, 1)",
                                    'pointBorderColor' => "#fff",
                                    'pointHoverBackgroundColor' => "#fff",
                                    'pointHoverBorderColor' => "rgba(66, 139, 202, 1)",
                                    'data' => array_values($dailyData)
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
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h3 class="panel-title">Pembayaran Berdasarkan Metode</h3>
                </div>
                <div class="panel-body">
                    <?= \dosamigos\chartjs\ChartJs::widget([
                        'type' => 'pie',
                        'options' => [
                            'height' => 200,
                            'width' => 400,
                        ],
                        'data' => [
                            'labels' => array_keys($paymentMethodData),
                            'datasets' => [
                                [
                                    'data' => array_values($paymentMethodData),
                                    'backgroundColor' => ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'],
                                ]
                            ]
                        ],
                        'clientOptions' => [
                            'responsive' => true,
                            'legend' => [
                                'position' => 'right'
                            ],
                            'tooltips' => [
                                'callbacks' => [
                                    'label' => new \yii\web\JsExpression('
                                        function(tooltipItem, data) {
                                            var dataset = data.datasets[tooltipItem.datasetIndex];
                                            var total = dataset.data.reduce(function(previousValue, currentValue) {
                                                return previousValue + currentValue;
                                            });
                                            var currentValue = dataset.data[tooltipItem.index];
                                            var percentage = Math.round((currentValue/total) * 100);
                                            return data.labels[tooltipItem.index] + ": Rp " + 
                                                currentValue.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".") + 
                                                " (" + percentage + "%)";
                                        }
                                    ')
                                ]
                            ]
                        ]
                    ]); ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="panel panel-warning">
                <div class="panel-heading">
                    <h3 class="panel-title">Ringkasan Pembayaran</h3>
                </div>
                <div class="panel-body">
                    <table class="table table-striped table-bordered">
                        <tr>
                            <th>Metode Pembayaran</th>
                            <th>Jumlah Transaksi</th>
                            <th>Total</th>
                        </tr>
                        <?php foreach ($paymentMethodData as $method => $amount): ?>
                        <tr>
                            <td><?= $method ?></td>
                            <td class="text-center">
                                <?= isset($paymentMethodCount[$method]) ? $paymentMethodCount[$method] : 0 ?>
                            </td>
                            <td class="text-right">Rp. <?= Yii::$app->formatter->asDecimal($amount, 0) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="info">
                            <th>Total</th>
                            <th class="text-center"><?= array_sum($paymentMethodCount ?? []) ?></th>
                            <th class="text-right">Rp. <?= Yii::$app->formatter->asDecimal($totalAmount, 0) ?></th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Detail Transaksi</h3>
        </div>
        <div class="panel-body">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute' => 'payment_date',
                        'value' => function ($model) {
                            return Yii::$app->formatter->asDate($model->payment_date);
                        },
                    ],
                    [
                        'attribute' => 'medical_record_id',
                        'label' => 'Pasien',
                        'value' => function ($model) {
                            return $model->medicalRecord && $model->medicalRecord->patient
                                ? $model->medicalRecord->patient->name 
                                : '-';
                        },
                    ],
                    [
                        'attribute' => 'payment_method',
                        'value' => function ($model) {
                            return $model->getPaymentMethodLabel();
                        },
                    ],
                    [
                        'attribute' => 'amount',
                        'value' => function ($model) {
                            return 'Rp. ' . Yii::$app->formatter->asDecimal($model->amount, 0);
                        },
                    ],
                    [
                        'attribute' => 'created_by',
                        'value' => function ($model) {
                            return $model->createdBy ? $model->createdBy->username : '-';
                        },
                    ],
                ],
            ]); ?>
        </div>
    </div>

</div>

<?php
// Add print-specific CSS
$this->registerCss('
    @media print {
        .panel-default, .panel-heading, .panel-body {
            border: none !important;
        }
        .panel {
            box-shadow: none !important;
        }
        .grid-view {
            font-size: 12px;
        }
        .summary, .pagination, form, .btn, .kv-panel-before {
            display: none !important;
        }
        a {
            text-decoration: none !important;
            color: #000 !important;
        }
    }
');
?>
