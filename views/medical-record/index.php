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
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                            'title' => 'Lihat',
                            'data-pjax' => '0',
                        ]);
                    },
                    'update' => function ($url, $model) {
                        // Only allow updating records that are not completed
                        return $model->status !== \app\models\MedicalRecord::STATUS_COMPLETED
                            ? Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                                'title' => 'Ubah',
                                'data-pjax' => '0',
                            ])
                            : '';
                    },
                    'delete' => function ($url, $model) {
                        // Only allow deleting records that are not completed
                        return $model->status !== \app\models\MedicalRecord::STATUS_COMPLETED
                            ? Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                                'title' => 'Hapus',
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
