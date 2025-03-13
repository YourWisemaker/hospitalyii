<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Daftar Tindakan';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="treatment-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Tambah Tindakan', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'code',
            'name',
            [
                'attribute' => 'category',
                'value' => function ($model) {
                    return $model->getCategoryLabel();
                },
            ],
            [
                'attribute' => 'price',
                'value' => function ($model) {
                    return 'Rp. ' . Yii::$app->formatter->asDecimal($model->price, 0);
                },
            ],
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    return $model->status ? 'Aktif' : 'Tidak Aktif';
                },
                'contentOptions' => function ($model, $key, $index, $column) {
                    return $model->status ? ['class' => 'success'] : ['class' => 'danger'];
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
                            'data-confirm' => 'Apakah Anda yakin ingin menghapus tindakan ini?',
                            'data-method' => 'post',
                            'data-pjax' => '0',
                        ]);
                    },
                ]
            ],
        ],
    ]); ?>

</div>
