<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\Patient */
/* @var $medicalRecords yii\data\ActiveDataProvider */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Pasien', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="patient-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Ubah', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Hapus', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Apakah Anda yakin ingin menghapus pasien ini?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Tambah Rekam Medis', ['/medical-record/create', 'patient_id' => $model->id], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Kembali', ['index'], ['class' => 'btn btn-default']) ?>
    </p>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Informasi Pasien</h3>
        </div>
        <div class="panel-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'registration_number',
                    'name',
                    [
                        'attribute' => 'gender',
                        'value' => function ($model) {
                            return $model->genderLabel; // Use the model's getGenderLabel() method
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
                    'contact',  // Changed from 'phone' to 'contact'
                    'address:ntext',
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

    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">Riwayat Rekam Medis</h3>
        </div>
        <div class="panel-body">
            <?= GridView::widget([
                'dataProvider' => $medicalRecords,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute' => 'treatment_date',
                        'value' => function ($model) {
                            return Yii::$app->formatter->asDatetime($model->treatment_date);
                        },
                    ],
                    [
                        'attribute' => 'doctor_id',
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
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view}',
                        'buttons' => [
                            'view' => function ($url, $model) {
                                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['/medical-record/view', 'id' => $model->id], [
                                    'title' => 'Lihat Rekam Medis',
                                    'data-pjax' => '0',
                                ]);
                            },
                        ],
                    ],
                ],
            ]); ?>
        </div>
    </div>

</div>
