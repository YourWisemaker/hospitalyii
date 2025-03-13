<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Daftar Obat';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="medicine-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Tambah Obat', ['create'], ['class' => 'btn btn-success']) ?>
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
                'attribute' => 'stock',
                'contentOptions' => function ($model) {
                    if ($model->stock <= $model->min_stock) {
                        return ['class' => 'danger'];
                    } elseif ($model->stock <= ($model->min_stock * 2)) {
                        return ['class' => 'warning'];
                    } else {
                        return ['class' => 'success'];
                    }
                },
            ],
            [
                'attribute' => 'purchase_price',
                'value' => function ($model) {
                    return 'Rp. ' . Yii::$app->formatter->asDecimal($model->purchase_price, 0);
                },
            ],
            [
                'attribute' => 'sell_price',
                'value' => function ($model) {
                    return 'Rp. ' . Yii::$app->formatter->asDecimal($model->sell_price, 0);
                },
            ],
            'unit',
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Aksi',
                'headerOptions' => ['style' => 'width: 200px; text-align: center;'],
                'contentOptions' => ['style' => 'text-align: center; white-space: nowrap;'],
                'template' => '{view} {update} {delete} {stock}',
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
                            'data-confirm' => 'Apakah Anda yakin ingin menghapus obat ini?',
                            'data-method' => 'post',
                            'data-pjax' => '0',
                        ]);
                    },
                    'stock' => function ($url, $model) {
                        return Html::a('<i class="fa fa-plus-circle"></i>', ['update-stock', 'id' => $model->id], [
                            'title' => 'Update Stok',
                            'class' => 'btn btn-sm btn-success',
                            'style' => 'margin: 2px;',
                            'data-pjax' => '0',
                        ]);
                    },
                ]
            ],
        ],
    ]); ?>

</div>
