<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Rekam Medis';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="medical-record-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Tambah Rekam Medis Baru', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'treatment_date',
                'value' => function ($model) {
                    return Yii::$app->formatter->asDatetime($model->treatment_date);
                },
            ],
            [
                'attribute' => 'patient_id',
                'label' => 'Pasien',
                'value' => function ($model) {
                    return $model->patient ? $model->patient->name . ' (' . $model->patient->registration_number . ')' : '-';
                },
            ],
            [
                'attribute' => 'doctor_id',
                'label' => 'Dokter',
                'value' => function ($model) {
                    return $model->doctor ? $model->doctor->name : '-';
                },
            ],
            'complaint:ntext',
            'diagnosis:ntext',
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    return $model->getStatusLabel();
                },
                'contentOptions' => function ($model) {
                    if ($model->status === \app\models\MedicalRecord::STATUS_COMPLETED) {
                        return ['class' => 'success'];
                    } elseif ($model->status === \app\models\MedicalRecord::STATUS_WAITING_PAYMENT) {
                        return ['class' => 'warning'];
                    } else {
                        return ['class' => 'info'];
                    }
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Aksi',
                'headerOptions' => ['style' => 'width: 180px; text-align: center;'],
                'contentOptions' => ['style' => 'text-align: center; white-space: nowrap;'],
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<i class="fa fa-eye"></i>', $url, [
                            'title' => 'Lihat',
                            'class' => 'btn btn-sm btn-info',
                            'style' => 'margin: 2px;',
                            'data-pjax' => '0',
                        ]);
                    },
                    'update' => function ($url, $model) {
                        // Only allow updating records that are not completed
                        return $model->status !== \app\models\MedicalRecord::STATUS_COMPLETED
                            ? Html::a('<i class="fa fa-pencil"></i>', $url, [
                                'title' => 'Ubah',
                                'class' => 'btn btn-sm btn-warning',
                                'style' => 'margin: 2px;',
                                'data-pjax' => '0',
                            ])
                            : '';
                    },
                    'delete' => function ($url, $model) {
                        // Only allow deleting records that are not completed
                        return $model->status !== \app\models\MedicalRecord::STATUS_COMPLETED
                            ? Html::a('<i class="fa fa-trash"></i>', $url, [
                                'title' => 'Hapus',
                                'class' => 'btn btn-sm btn-danger',
                                'style' => 'margin: 2px;',
                                'data-confirm' => 'Apakah Anda yakin ingin menghapus rekam medis ini?',
                                'data-method' => 'post',
                                'data-pjax' => '0',
                            ])
                            : '';
                    },
                ]
            ],
        ],
    ]); ?>

</div>
