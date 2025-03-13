<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Medicine */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Daftar Obat', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="medicine-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Ubah', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Hapus', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Apakah Anda yakin ingin menghapus obat ini?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Update Stok', ['update-stock', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Kembali', ['index'], ['class' => 'btn btn-default']) ?>
    </p>

    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Informasi Obat</h3>
                </div>
                <div class="panel-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                            'code',
                            'name',
                            [
                                'attribute' => 'category',
                                'value' => function ($model) {
                                    return $model->getCategoryLabel();
                                },
                            ],
                            'unit',
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
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">Informasi Stok & Harga</h3>
                </div>
                <div class="panel-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            [
                                'attribute' => 'stock',
                                'value' => function ($model) {
                                    return $model->stock . ' ' . $model->unit;
                                },
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
                            'min_stock',
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
                            [
                                'attribute' => '',
                                'label' => 'Margin',
                                'value' => function ($model) {
                                    $margin = $model->sell_price - $model->purchase_price;
                                    $marginPercent = ($margin / $model->purchase_price) * 100;
                                    return 'Rp. ' . Yii::$app->formatter->asDecimal($margin, 0) . 
                                           ' (' . Yii::$app->formatter->asDecimal($marginPercent, 2) . '%)';
                                },
                            ],
                        ],
                    ]) ?>
                    
                    <?php if ($model->stock <= $model->min_stock): ?>
                        <div class="alert alert-danger">
                            <strong>Perhatian!</strong> Stok obat ini di bawah jumlah minimum. Segera lakukan penambahan stok.
                        </div>
                    <?php elseif ($model->stock <= ($model->min_stock * 2)): ?>
                        <div class="alert alert-warning">
                            <strong>Perhatian!</strong> Stok obat ini mendekati jumlah minimum.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</div>
