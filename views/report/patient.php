<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datecontrol\DateControl;
use dosamigos\chartjs\ChartJs;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $startDate string */
/* @var $endDate string */
/* @var $totalPatients int */
/* @var $newPatients int */
/* @var $patientsByGender array */
/* @var $ageGroupData array */
/* @var $patientsByRegion array */
/* @var $patientsProvider yii\data\ActiveDataProvider */

$this->title = 'Laporan Pasien';
$this->params['breadcrumbs'][] = ['label' => 'Laporan', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Prepare data for gender chart
$genderLabels = [];
$genderData = [];
foreach ($patientsByGender as $gender) {
    $label = $gender['gender'] === 'L' ? 'Laki-laki' : ($gender['gender'] === 'P' ? 'Perempuan' : 'Lainnya');
    $genderLabels[] = $label;
    $genderData[] = (int)$gender['count'];
}

// Prepare data for age groups chart
$ageLabels = array_keys($ageGroupData);
$ageData = array_values($ageGroupData);

// Prepare data for region chart
$regionLabels = [];
$regionData = [];
foreach ($patientsByRegion as $region) {
    $regionLabels[] = $region['region_name'] ?: 'Tidak ada wilayah';
    $regionData[] = (int)$region['count'];
}

?>
<div class="report-patient">
    <h1><?= Html::encode($this->title) ?></h1>
    
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Filter Periode</h3>
                </div>
                <div class="panel-body">
                    <?php $form = ActiveForm::begin(['method' => 'get', 'action' => ['patient'], 'options' => ['class' => 'form-inline']]); ?>
                    
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
                        <?= Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset', ['patient'], ['class' => 'btn btn-default']) ?>
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
                    <h3 class="panel-title">Total Pasien</h3>
                </div>
                <div class="panel-body">
                    <h2 class="text-center"><?= $totalPatients ?></h2>
                    <p class="text-center text-muted">Total keseluruhan pasien yang terdaftar</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h3 class="panel-title">Pasien Baru</h3>
                </div>
                <div class="panel-body">
                    <h2 class="text-center"><?= $newPatients ?></h2>
                    <p class="text-center text-muted">Pasien baru yang terdaftar pada periode <?= Yii::$app->formatter->asDate($startDate) ?> hingga <?= Yii::$app->formatter->asDate($endDate) ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Pasien Berdasarkan Jenis Kelamin</h3>
                </div>
                <div class="panel-body">
                    <?= ChartJs::widget([
                        'type' => 'pie',
                        'data' => [
                            'labels' => $genderLabels,
                            'datasets' => [
                                [
                                    'data' => $genderData,
                                    'backgroundColor' => ["#3498db", "#e74c3c", "#95a5a6"],
                                    'borderColor' => ["#2980b9", "#c0392b", "#7f8c8d"],
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
                    <h3 class="panel-title">Pasien Berdasarkan Kelompok Usia</h3>
                </div>
                <div class="panel-body">
                    <?= ChartJs::widget([
                        'type' => 'bar',
                        'data' => [
                            'labels' => $ageLabels,
                            'datasets' => [
                                [
                                    'label' => 'Jumlah Pasien',
                                    'data' => $ageData,
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
                    <h3 class="panel-title">Pasien Berdasarkan Wilayah</h3>
                </div>
                <div class="panel-body">
                    <?= ChartJs::widget([
                        'type' => 'horizontalBar',
                        'data' => [
                            'labels' => $regionLabels,
                            'datasets' => [
                                [
                                    'label' => 'Jumlah Pasien',
                                    'data' => $regionData,
                                    'backgroundColor' => "rgba(46, 204, 113, 0.6)",
                                    'borderColor' => "rgba(46, 204, 113, 1)",
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
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Daftar Pasien Baru (Periode: <?= Yii::$app->formatter->asDate($startDate) ?> - <?= Yii::$app->formatter->asDate($endDate) ?>)</h3>
                </div>
                <div class="panel-body">
                    <?= GridView::widget([
                        'dataProvider' => $patientsProvider,
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
                            'registration_number',
                            'name',
                            [
                                'attribute' => 'gender',
                                'value' => function ($model) {
                                    return $model->gender === 'L' ? 'Laki-laki' : ($model->gender === 'P' ? 'Perempuan' : $model->gender);
                                }
                            ],
                            [
                                'attribute' => 'birth_date',
                                'format' => 'date',
                            ],
                            [
                                'attribute' => 'region_id',
                                'value' => 'region.name',
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
                                        return Html::a('<i class="glyphicon glyphicon-eye-open"></i>', ['/patient/view', 'id' => $model->id], [
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
            <?= Html::a('<i class="glyphicon glyphicon-print"></i> Cetak Laporan', ['patient', 'start_date' => $startDate, 'end_date' => $endDate, 'format' => 'pdf'], [
                'class' => 'btn btn-success',
                'target' => '_blank'
            ]) ?>
            <?= Html::a('<i class="glyphicon glyphicon-download"></i> Export Excel', ['patient', 'start_date' => $startDate, 'end_date' => $endDate, 'format' => 'excel'], [
                'class' => 'btn btn-primary'
            ]) ?>
        </div>
    </div>
</div>
