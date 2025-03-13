<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\MedicalRecord */
/* @var $treatmentDetails yii\data\ActiveDataProvider */
/* @var $medicineDetails yii\data\ActiveDataProvider */
/* @var $payments yii\data\ActiveDataProvider */

$this->title = 'Rekam Medis: ' . ($model->patient ? $model->patient->name : 'Pasien');
$this->params['breadcrumbs'][] = ['label' => 'Rekam Medis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$isCompleted = $model->status === \app\models\MedicalRecord::STATUS_COMPLETED;
?>
<div class="medical-record-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if (!$isCompleted): ?>
            <?= Html::a('Ubah', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Hapus', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Apakah Anda yakin ingin menghapus rekam medis ini?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif; ?>
        <?= Html::a('Tambah Tindakan', ['add-treatment', 'id' => $model->id], ['class' => 'btn btn-info' . ($isCompleted ? ' disabled' : '')]) ?>
        <?= Html::a('Tambah Obat', ['add-medicine', 'id' => $model->id], ['class' => 'btn btn-warning' . ($isCompleted ? ' disabled' : '')]) ?>
        <?= Html::a('Proses Pembayaran', ['payment', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Kembali', ['index'], ['class' => 'btn btn-default']) ?>
    </p>

    <!-- Information Panels -->
    <div class="row">
        <!-- Medical Record Info -->
        <div class="col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Informasi Rekam Medis</h3>
                </div>
                <div class="panel-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                            [
                                'attribute' => 'treatment_date',
                                'value' => function ($model) {
                                    return Yii::$app->formatter->asDatetime($model->treatment_date);
                                },
                            ],
                            [
                                'attribute' => 'patient_id',
                                'value' => function ($model) {
                                    return $model->patient ? $model->patient->name . ' (' . $model->patient->registration_number . ')' : '-';
                                },
                            ],
                            [
                                'attribute' => 'doctor_id',
                                'value' => function ($model) {
                                    return $model->doctor ? $model->doctor->name : '-';
                                },
                            ],
                            [
                                'attribute' => 'status',
                                'value' => function ($model) {
                                    return $model->getStatusLabel();
                                },
                            ],
                            [
                                'attribute' => 'created_at',
                                'value' => function ($model) {
                                    return Yii::$app->formatter->asDatetime($model->created_at);
                                },
                            ],
                            [
                                'attribute' => 'updated_at',
                                'value' => function ($model) {
                                    return Yii::$app->formatter->asDatetime($model->updated_at);
                                },
                            ],
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
        
        <!-- Patient Info -->
        <div class="col-md-6">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">Informasi Pasien</h3>
                </div>
                <div class="panel-body">
                    <?php if ($model->patient): ?>
                        <?= DetailView::widget([
                            'model' => $model->patient,
                            'attributes' => [
                                'registration_number',
                                'name',
                                [
                                    'attribute' => 'gender',
                                    'value' => function ($model) {
                                        return $model->gender === 'L' ? 'Laki-laki' : 'Perempuan';
                                    },
                                ],
                                'birth_date',
                                [
                                    'attribute' => 'birth_date',
                                    'label' => 'Usia',
                                    'value' => function ($model) {
                                        return $model->getAge() . ' tahun';
                                    },
                                ],
                                'phone',
                                'address:ntext',
                            ],
                        ]) ?>
                    <?php else: ?>
                        <div class="alert alert-warning">Data pasien tidak ditemukan</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Medical Diagnosis -->
    <div class="panel panel-success">
        <div class="panel-heading">
            <h3 class="panel-title">Diagnosis & Keluhan</h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <h4>Keluhan Pasien</h4>
                    <p><?= Yii::$app->formatter->asNtext($model->complaint) ?></p>
                </div>
                <div class="col-md-6">
                    <h4>Diagnosis</h4>
                    <p><?= Yii::$app->formatter->asNtext($model->diagnosis) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Treatment Details -->
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Daftar Tindakan</h3>
        </div>
        <div class="panel-body">
            <?= GridView::widget([
                'dataProvider' => $treatmentDetails,
                'showHeader' => true,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute' => 'treatment_id',
                        'value' => function ($model) {
                            return $model->treatment ? $model->treatment->name : '-';
                        },
                    ],
                    'quantity',
                    [
                        'attribute' => 'price',
                        'value' => function ($model) {
                            return 'Rp. ' . Yii::$app->formatter->asDecimal($model->price, 0);
                        },
                    ],
                    [
                        'label' => 'Subtotal',
                        'value' => function ($model) {
                            return 'Rp. ' . Yii::$app->formatter->asDecimal($model->getSubtotal(), 0);
                        },
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{delete}',
                        'buttons' => [
                            'delete' => function ($url, $model) use ($isCompleted) {
                                return !$isCompleted
                                    ? Html::a('<span class="glyphicon glyphicon-trash"></span>', ['remove-treatment', 'id' => $model->id], [
                                        'title' => 'Hapus',
                                        'data-confirm' => 'Apakah Anda yakin ingin menghapus tindakan ini?',
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ])
                                    : '';
                            },
                        ],
                    ],
                ],
            ]); ?>
        </div>
    </div>

    <!-- Medicine Details -->
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Daftar Obat</h3>
        </div>
        <div class="panel-body">
            <?= GridView::widget([
                'dataProvider' => $medicineDetails,
                'showHeader' => true,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute' => 'medicine_id',
                        'value' => function ($model) {
                            return $model->medicine ? $model->medicine->name : '-';
                        },
                    ],
                    'quantity',
                    [
                        'attribute' => 'price',
                        'value' => function ($model) {
                            return 'Rp. ' . Yii::$app->formatter->asDecimal($model->price, 0);
                        },
                    ],
                    [
                        'label' => 'Subtotal',
                        'value' => function ($model) {
                            return 'Rp. ' . Yii::$app->formatter->asDecimal($model->getSubtotal(), 0);
                        },
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{delete}',
                        'buttons' => [
                            'delete' => function ($url, $model) use ($isCompleted) {
                                return !$isCompleted
                                    ? Html::a('<span class="glyphicon glyphicon-trash"></span>', ['remove-medicine', 'id' => $model->id], [
                                        'title' => 'Hapus',
                                        'data-confirm' => 'Apakah Anda yakin ingin menghapus obat ini?',
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ])
                                    : '';
                            },
                        ],
                    ],
                ],
            ]); ?>
        </div>
    </div>

    <!-- Billing Information -->
    <div class="panel panel-warning">
        <div class="panel-heading">
            <h3 class="panel-title">Informasi Pembayaran</h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <h4>Rincian Biaya</h4>
                    <table class="table table-striped">
                        <tr>
                            <th>Total Biaya Tindakan</th>
                            <td>Rp. <?= Yii::$app->formatter->asDecimal($model->getTreatmentTotal(), 0) ?></td>
                        </tr>
                        <tr>
                            <th>Total Biaya Obat</th>
                            <td>Rp. <?= Yii::$app->formatter->asDecimal($model->getMedicineTotal(), 0) ?></td>
                        </tr>
                        <tr class="success">
                            <th>Total Keseluruhan</th>
                            <td><strong>Rp. <?= Yii::$app->formatter->asDecimal($model->getTotalAmount(), 0) ?></strong></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h4>Pembayaran</h4>
                    <?= GridView::widget([
                        'dataProvider' => $payments,
                        'showHeader' => true,
                        'columns' => [
                            [
                                'attribute' => 'payment_date',
                                'value' => function ($model) {
                                    return Yii::$app->formatter->asDate($model->payment_date);
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
                                'attribute' => 'status',
                                'value' => function ($model) {
                                    return $model->getStatusLabel();
                                },
                                'contentOptions' => function ($model) {
                                    return $model->status === \app\models\Payment::STATUS_PAID 
                                        ? ['class' => 'success'] 
                                        : ['class' => 'warning'];
                                },
                            ],
                        ],
                    ]); ?>
                    
                    <?php if (!$isCompleted): ?>
                        <p class="text-right">
                            <?= Html::a('Tambah Pembayaran', ['payment', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
