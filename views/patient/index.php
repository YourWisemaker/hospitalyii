<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchTerm string */

$this->title = 'Pasien';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="patient-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-8">
            <p>
                <?= Html::a('Tambah Pasien Baru', ['create'], ['class' => 'btn btn-success']) ?>
            </p>
        </div>
        <div class="col-md-4">
            <form action="<?= \yii\helpers\Url::to(['patient/search']) ?>" method="get" class="form-inline">
                <div class="input-group" style="width: 100%;">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama, no. pasien atau telepon" value="<?= Html::encode(isset($searchTerm) ? $searchTerm : '') ?>">
                    <span class="input-group-btn">
                        <button class="btn btn-primary" type="submit"><i class="glyphicon glyphicon-search"></i> Cari</button>
                    </span>
                </div>
            </form>
        </div>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'registration_number',
            'name',
            [
                'attribute' => 'gender',
                'value' => function ($model) {
                    return $model->getGenderLabel();
                }
            ],
            'birth_date',
            'contact',
            [
                'attribute' => 'address',
                'format' => 'ntext',
                'contentOptions' => ['style' => 'max-width: 200px; white-space: normal; word-wrap: break-word;'],
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
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                            'title' => 'Ubah',
                            'data-pjax' => '0',
                        ]);
                    },
                    'delete' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                            'title' => 'Hapus',
                            'data-confirm' => 'Apakah Anda yakin ingin menghapus pasien ini?',
                            'data-method' => 'post',
                            'data-pjax' => '0',
                        ]);
                    },
                ]
            ],
        ],
    ]); ?>

</div>
