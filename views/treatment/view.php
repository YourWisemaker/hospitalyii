<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Treatment */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Daftar Tindakan', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="treatment-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Ubah', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Hapus', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Apakah Anda yakin ingin menghapus tindakan ini?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Kembali', ['index'], ['class' => 'btn btn-default']) ?>
    </p>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Detail Tindakan</h3>
        </div>
        <div class="panel-body">
            <?php
            $attributes = [
                'id',
                'code',
                'name',
                [
                    'attribute' => 'category',
                    'value' => $model->getCategoryLabel(),
                ],
                [
                    'attribute' => 'price',
                    'value' => 'Rp. ' . Yii::$app->formatter->asDecimal($model->price, 0),
                ],
                [
                    'attribute' => 'status',
                    'value' => $model->status ? 'Aktif' : 'Tidak Aktif',
                    'contentOptions' => $model->status ? ['class' => 'success'] : ['class' => 'danger'],
                ],
                'description:ntext',
                [
                    'attribute' => 'created_at',
                    'value' => Yii::$app->formatter->asDatetime($model->created_at),
                ],
                [
                    'attribute' => 'updated_at',
                    'value' => Yii::$app->formatter->asDatetime($model->updated_at),
                ],
                [
                    'attribute' => 'created_by',
                    'value' => $model->createdBy ? $model->createdBy->username : '-',
                ],
                [
                    'attribute' => 'updated_by',
                    'value' => $model->updatedBy ? $model->updatedBy->username : '-',
                ],
            ];
            
            echo DetailView::widget([
                'model' => $model,
                'attributes' => $attributes,
            ]);
            ?>
        </div>
    </div>

</div>
