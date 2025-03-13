<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Wilayah';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="region-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Tambah Wilayah', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            'description:ntext',
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
                            'data-confirm' => 'Apakah Anda yakin ingin menghapus wilayah ini?',
                            'data-method' => 'post',
                            'data-pjax' => '0',
                        ]);
                    },
                ]
            ],
        ],
    ]); ?>

</div>
