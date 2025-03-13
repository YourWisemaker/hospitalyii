<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Employee */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Daftar Karyawan', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="employee-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Ubah', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Hapus', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Apakah Anda yakin ingin menghapus karyawan ini?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Kembali', ['index'], ['class' => 'btn btn-default']) ?>
    </p>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Detail Karyawan</h3>
        </div>
        <div class="panel-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'employee_number',
                    'name',
                    [
                        'attribute' => 'position',
                        'value' => function ($model) {
                            return $model->getPositionLabel();
                        },
                    ],
                    [
                        'attribute' => 'status',
                        'value' => function ($model) {
                            return $model->status ? 'Aktif' : 'Tidak Aktif';
                        },
                    ],
                    'phone',
                    'email:email',
                    'birth_date',
                    [
                        'attribute' => 'gender',
                        'value' => function ($model) {
                            return $model->gender === 'L' ? 'Laki-laki' : 'Perempuan';
                        },
                    ],
                    [
                        'attribute' => 'region_id',
                        'value' => function ($model) {
                            return $model->region ? $model->region->name : '-';
                        },
                    ],
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
                    [
                        'attribute' => 'created_by',
                        'value' => function ($model) {
                            return $model->createdBy ? $model->createdBy->username : '-';
                        },
                    ],
                    [
                        'attribute' => 'updated_by',
                        'value' => function ($model) {
                            return $model->updatedBy ? $model->updatedBy->username : '-';
                        },
                    ],
                ],
            ]) ?>
        </div>
    </div>

</div>
