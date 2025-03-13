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
                        <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Cari</button>
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
                        return Html::a('<i class="fa fa-pencil"></i>', $url, [
                            'title' => 'Ubah',
                            'class' => 'btn btn-sm btn-warning',
                            'style' => 'margin: 2px;',
                            'data-pjax' => '0',
                        ]);
                    },
                    'delete' => function ($url, $model) {
                        return Html::a('<i class="fa fa-trash"></i>', $url, [
                            'title' => 'Hapus',
                            'class' => 'btn btn-sm btn-danger',
                            'style' => 'margin: 2px;',
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
