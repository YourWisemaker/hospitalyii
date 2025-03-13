<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datecontrol\DateControl;
use dosamigos\chartjs\ChartJs;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $startDate string */
/* @var $endDate string */
/* @var $totalTreatments int */
/* @var $periodTreatments int */
/* @var $totalTreatmentRevenue float */
/* @var $topTreatments array */
/* @var $treatmentsByCategory array */
/* @var $monthlyTreatmentsData array */
/* @var $treatmentsProvider yii\data\ActiveDataProvider */

$this->title = 'Laporan Tindakan';
$this->params['breadcrumbs'][] = ['label' => 'Laporan', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Prepare data for top treatments chart
$topTreatmentLabels = [];
$topTreatmentData = [];
foreach ($topTreatments as $treatment) {
    $topTreatmentLabels[] = $treatment['name'] . ' (' . $treatment['count'] . ')';
    $topTreatmentData[] = (int)$treatment['count'];
}

// Prepare data for treatment categories chart
$categoryLabels = [];
$categoryData = [];
foreach ($treatmentsByCategory as $category) {
    $categoryLabels[] = $category['category'] ?: 'Tidak ada kategori';
    $categoryData[] = (int)$category['count'];
}

?>
<div class="report-treatment">
    <h1><?= Html::encode($this->title) ?></h1>
    
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Filter Periode</h3>
                </div>
                <div class="panel-body">
                    <?php $form = ActiveForm::begin(['method' => 'get', 'action' => ['treatment'], 'options' => ['class' => 'form-inline']]); ?>
                    
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
                        <?= Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset', ['treatment'], ['class' => 'btn btn-default']) ?>
                    </div>
                    
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Total Tindakan</h3>
                </div>
                <div class="panel-body">
                    <h2 class="text-center"><?= $totalTreatments ?></h2>
                    <p class="text-center text-muted">Jumlah total jenis tindakan yang tersedia</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h3 class="panel-title">Tindakan Periode</h3>
                </div>
                <div class="panel-body">
                    <h2 class="text-center"><?= $periodTreatments ?></h2>
                    <p class="text-center text-muted">Jumlah tindakan pada periode <?= Yii::$app->formatter->asDate($startDate) ?> hingga <?= Yii::$app->formatter->asDate($endDate) ?></p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">Pendapatan dari Tindakan</h3>
                </div>
                <div class="panel-body">
                    <h2 class="text-center">Rp. <?= Yii::$app->formatter->asDecimal($totalTreatmentRevenue, 0) ?></h2>
                    <p class="text-center text-muted">Total pendapatan dari tindakan pada periode</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Tindakan Terbanyak Dilakukan (Periode: <?= Yii::$app->formatter->asDate($startDate) ?> - <?= Yii::$app->formatter->asDate($endDate) ?>)</h3>
                </div>
                <div class="panel-body">
                    <?= ChartJs::widget([
                        'type' => 'horizontalBar',
                        'data' => [
                            'labels' => $topTreatmentLabels,
                            'datasets' => [
                                [
                                    'label' => 'Frekuensi Tindakan',
                                    'data' => $topTreatmentData,
                                    'backgroundColor' => "rgba(231, 76, 60, 0.6)",
                                    'borderColor' => "rgba(231, 76, 60, 1)",
                                    'borderWidth' => 1,
                                ]
                            ]
                        ],
                        'clientOptions' => [
                            'responsive' => true,
                            'scales' => [
                                'xAxes' => [
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
        
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Distribusi Tindakan Berdasarkan Kategori</h3>
                </div>
                <div class="panel-body">
                    <?= ChartJs::widget([
                        'type' => 'pie',
                        'data' => [
                            'labels' => $categoryLabels,
                            'datasets' => [
                                [
                                    'data' => $categoryData,
                                    'backgroundColor' => ["#3498db", "#e74c3c", "#f39c12", "#2ecc71", "#9b59b6", "#1abc9c", "#d35400", "#c0392b"],
                                    'borderColor' => ["#2980b9", "#c0392b", "#d35400", "#27ae60", "#8e44ad", "#16a085", "#e67e22", "#c0392b"],
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
                    <h3 class="panel-title">Tren Tindakan Bulanan (12 Bulan Terakhir)</h3>
                </div>
                <div class="panel-body">
                    <?= ChartJs::widget([
                        'type' => 'line',
                        'data' => [
                            'labels' => array_keys($monthlyTreatmentsData),
                            'datasets' => [
                                [
                                    'label' => 'Jumlah Tindakan',
                                    'data' => array_values($monthlyTreatmentsData),
                                    'backgroundColor' => "rgba(155, 89, 182, 0.2)",
                                    'borderColor' => "rgba(155, 89, 182, 1)",
                                    'pointBackgroundColor' => "rgba(155, 89, 182, 1)",
                                    'pointBorderColor' => "#fff",
                                    'pointHoverBackgroundColor' => "#fff",
                                    'pointHoverBorderColor' => "rgba(155, 89, 182, 1)",
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
                    <h3 class="panel-title">Daftar Tindakan</h3>
                </div>
                <div class="panel-body">
                    <?= GridView::widget([
                        'dataProvider' => $treatmentsProvider,
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
                            'code',
                            'name',
                            'category',
                            [
                                'attribute' => 'price',
                                'value' => function ($model) {
                                    return 'Rp. ' . Yii::$app->formatter->asDecimal($model->price, 0);
                                },
                            ],
                            [
                                'label' => 'Penggunaan Periode',
                                'value' => function ($model) use ($startDate, $endDate) {
                                    return $model->getUsageCount($startDate, $endDate);
                                },
                            ],
                            [
                                'label' => 'Pendapatan Periode',
                                'value' => function ($model) use ($startDate, $endDate) {
                                    $usage = $model->getUsageCount($startDate, $endDate);
                                    return 'Rp. ' . Yii::$app->formatter->asDecimal($usage * $model->price, 0);
                                },
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{view}',
                                'buttons' => [
                                    'view' => function ($url, $model, $key) {
                                        return Html::a('<i class="glyphicon glyphicon-eye-open"></i>', ['/treatment/view', 'id' => $model->id], [
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
            <?= Html::a('<i class="glyphicon glyphicon-print"></i> Cetak Laporan', ['treatment', 'start_date' => $startDate, 'end_date' => $endDate, 'format' => 'pdf'], [
                'class' => 'btn btn-success',
                'target' => '_blank'
            ]) ?>
            <?= Html::a('<i class="glyphicon glyphicon-download"></i> Export Excel', ['treatment', 'start_date' => $startDate, 'end_date' => $endDate, 'format' => 'excel'], [
                'class' => 'btn btn-primary'
            ]) ?>
        </div>
    </div>
</div>
