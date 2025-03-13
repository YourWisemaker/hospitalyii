<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datecontrol\DateControl;
use dosamigos\chartjs\ChartJs;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $startDate string */
/* @var $endDate string */
/* @var $totalMedicines int */
/* @var $totalMedicineValue float */
/* @var $lowStockCount int */
/* @var $topMedicines array */
/* @var $medicinesByCategory array */
/* @var $medicinesProvider yii\data\ActiveDataProvider */

$this->title = 'Laporan Obat';
$this->params['breadcrumbs'][] = ['label' => 'Laporan', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Prepare data for top medicines chart
$topMedicineLabels = [];
$topMedicineData = [];
foreach ($topMedicines as $medicine) {
    $topMedicineLabels[] = $medicine['name'] . ' (' . $medicine['count'] . ')';
    $topMedicineData[] = (int)$medicine['count'];
}

// Prepare data for medicine categories chart
$categoryLabels = [];
$categoryData = [];
foreach ($medicinesByCategory as $category) {
    $categoryLabels[] = $category['category'] ?: 'Tidak ada kategori';
    $categoryData[] = (int)$category['count'];
}

?>
<div class="report-medicine">
    <h1><?= Html::encode($this->title) ?></h1>
    
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Filter Periode</h3>
                </div>
                <div class="panel-body">
                    <?php $form = ActiveForm::begin(['method' => 'get', 'action' => ['medicine'], 'options' => ['class' => 'form-inline']]); ?>
                    
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
                        <?= Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset', ['medicine'], ['class' => 'btn btn-default']) ?>
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
                    <h3 class="panel-title">Total Obat</h3>
                </div>
                <div class="panel-body">
                    <h2 class="text-center"><?= $totalMedicines ?></h2>
                    <p class="text-center text-muted">Jumlah total jenis obat yang tersedia</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h3 class="panel-title">Nilai Inventaris</h3>
                </div>
                <div class="panel-body">
                    <h2 class="text-center">Rp. <?= Yii::$app->formatter->asDecimal($totalMedicineValue, 0) ?></h2>
                    <p class="text-center text-muted">Total nilai aset obat saat ini</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="panel panel-danger">
                <div class="panel-heading">
                    <h3 class="panel-title">Stok Rendah</h3>
                </div>
                <div class="panel-body">
                    <h2 class="text-center"><?= $lowStockCount ?></h2>
                    <p class="text-center text-muted">Jumlah obat dengan stok di bawah minimum</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Obat Terbanyak Digunakan (Periode: <?= Yii::$app->formatter->asDate($startDate) ?> - <?= Yii::$app->formatter->asDate($endDate) ?>)</h3>
                </div>
                <div class="panel-body">
                    <?= ChartJs::widget([
                        'type' => 'horizontalBar',
                        'data' => [
                            'labels' => $topMedicineLabels,
                            'datasets' => [
                                [
                                    'label' => 'Frekuensi Pemakaian',
                                    'data' => $topMedicineData,
                                    'backgroundColor' => "rgba(52, 152, 219, 0.6)",
                                    'borderColor' => "rgba(52, 152, 219, 1)",
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
                    <h3 class="panel-title">Distribusi Obat Berdasarkan Kategori</h3>
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
                    <h3 class="panel-title">Daftar Inventaris Obat</h3>
                </div>
                <div class="panel-body">
                    <?= GridView::widget([
                        'dataProvider' => $medicinesProvider,
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
                            'code',
                            'name',
                            'category',
                            [
                                'attribute' => 'stock',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    $stockClass = $model->stock <= $model->minimum_stock ? 'label-danger' : 'label-success';
                                    return '<span class="label ' . $stockClass . '">' . $model->stock . '</span>';
                                },
                            ],
                            'minimum_stock',
                            [
                                'attribute' => 'price',
                                'value' => function ($model) {
                                    return 'Rp. ' . Yii::$app->formatter->asDecimal($model->price, 0);
                                },
                            ],
                            [
                                'label' => 'Nilai Total',
                                'value' => function ($model) {
                                    return 'Rp. ' . Yii::$app->formatter->asDecimal($model->stock * $model->price, 0);
                                },
                            ],
                            [
                                'attribute' => 'created_at',
                                'format' => 'datetime',
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{view}',
                                'buttons' => [
                                    'view' => function ($url, $model, $key) {
                                        return Html::a('<i class="glyphicon glyphicon-eye-open"></i>', ['/medicine/view', 'id' => $model->id], [
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
            <?= Html::a('<i class="glyphicon glyphicon-print"></i> Cetak Laporan', ['medicine', 'start_date' => $startDate, 'end_date' => $endDate, 'format' => 'pdf'], [
                'class' => 'btn btn-success',
                'target' => '_blank'
            ]) ?>
            <?= Html::a('<i class="glyphicon glyphicon-download"></i> Export Excel', ['medicine', 'start_date' => $startDate, 'end_date' => $endDate, 'format' => 'excel'], [
                'class' => 'btn btn-primary'
            ]) ?>
        </div>
    </div>
</div>
