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
                'template' => '{view} {update} {delete} {stock}',
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
                            'data-confirm' => 'Apakah Anda yakin ingin menghapus obat ini?',
                            'data-method' => 'post',
                            'data-pjax' => '0',
                        ]);
                    },
                    'stock' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-plus-sign"></span>', ['update-stock', 'id' => $model->id], [
                            'title' => 'Update Stok',
                            'data-pjax' => '0',
                        ]);
                    },
                ]
            ],
        ],
    ]); ?>

</div>
