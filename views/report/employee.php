<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datecontrol\DateControl;
use dosamigos\chartjs\ChartJs;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $startDate string */
/* @var $endDate string */
/* @var $totalEmployees int */
/* @var $activeEmployees int */
/* @var $employeesByPosition array */
/* @var $employeesByStatus array */
/* @var $employeesProvider yii\data\ActiveDataProvider */

$this->title = 'Laporan Karyawan';
$this->params['breadcrumbs'][] = ['label' => 'Laporan', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Prepare data for positions chart
$positionLabels = [];
$positionData = [];
foreach ($employeesByPosition as $position) {
    $positionLabels[] = $position['position'] ?: 'Tidak ada posisi';
    $positionData[] = (int)$position['count'];
}

// Prepare data for status chart
$statusLabels = [];
$statusData = [];
foreach ($employeesByStatus as $status) {
    $statusLabels[] = $status['is_active'] ? 'Aktif' : 'Tidak Aktif';
    $statusData[] = (int)$status['count'];
}

?>
<div class="report-employee">
    <h1><?= Html::encode($this->title) ?></h1>
    
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Filter Periode</h3>
                </div>
                <div class="panel-body">
                    <?php $form = ActiveForm::begin(['method' => 'get', 'action' => ['employee'], 'options' => ['class' => 'form-inline']]); ?>
                    
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
                        <?= Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset', ['employee'], ['class' => 'btn btn-default']) ?>
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
                    <h3 class="panel-title">Total Karyawan</h3>
                </div>
                <div class="panel-body">
                    <h2 class="text-center"><?= $totalEmployees ?></h2>
                    <p class="text-center text-muted">Jumlah total karyawan</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h3 class="panel-title">Karyawan Aktif</h3>
                </div>
                <div class="panel-body">
                    <h2 class="text-center"><?= $activeEmployees ?></h2>
                    <p class="text-center text-muted">Jumlah karyawan dengan status aktif</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Karyawan Berdasarkan Posisi</h3>
                </div>
                <div class="panel-body">
                    <?= ChartJs::widget([
                        'type' => 'pie',
                        'data' => [
                            'labels' => $positionLabels,
                            'datasets' => [
                                [
                                    'data' => $positionData,
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
        
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Karyawan Berdasarkan Status</h3>
                </div>
                <div class="panel-body">
                    <?= ChartJs::widget([
                        'type' => 'pie',
                        'data' => [
                            'labels' => $statusLabels,
                            'datasets' => [
                                [
                                    'data' => $statusData,
                                    'backgroundColor' => ["#2ecc71", "#e74c3c"],
                                    'borderColor' => ["#27ae60", "#c0392b"],
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
                    <h3 class="panel-title">Daftar Karyawan</h3>
                </div>
                <div class="panel-body">
                    <?= GridView::widget([
                        'dataProvider' => $employeesProvider,
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
                            'employee_number',
                            'name',
                            'position',
                            [
                                'attribute' => 'birth_date',
                                'format' => 'date',
                            ],
                            'phone',
                            'email:email',
                            [
                                'attribute' => 'is_active',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return $model->is_active ? 
                                        '<span class="label label-success">Aktif</span>' : 
                                        '<span class="label label-danger">Tidak Aktif</span>';
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
                                        return Html::a('<i class="glyphicon glyphicon-eye-open"></i>', ['/employee/view', 'id' => $model->id], [
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
            <?= Html::a('<i class="glyphicon glyphicon-print"></i> Cetak Laporan', ['employee', 'start_date' => $startDate, 'end_date' => $endDate, 'format' => 'pdf'], [
                'class' => 'btn btn-success',
                'target' => '_blank'
            ]) ?>
            <?= Html::a('<i class="glyphicon glyphicon-download"></i> Export Excel', ['employee', 'start_date' => $startDate, 'end_date' => $endDate, 'format' => 'excel'], [
                'class' => 'btn btn-primary'
            ]) ?>
        </div>
    </div>
</div>
