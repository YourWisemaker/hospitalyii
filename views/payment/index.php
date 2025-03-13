<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Daftar Pembayaran';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Lihat Laporan Pembayaran', ['report'], ['class' => 'btn btn-info']) ?>
    </p>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Filter Pembayaran</h3>
        </div>
        <div class="panel-body">
            <form method="get" class="form-horizontal">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label col-sm-4">Dari Tanggal</label>
                            <div class="col-sm-8">
                                <input type="date" name="start_date" class="form-control" 
                                    value="<?= Yii::$app->request->get('start_date') ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label col-sm-4">Sampai Tanggal</label>
                            <div class="col-sm-8">
                                <input type="date" name="end_date" class="form-control"
                                    value="<?= Yii::$app->request->get('end_date') ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label col-sm-4">Status</label>
                            <div class="col-sm-8">
                                <select name="status" class="form-control">
                                    <option value="">Semua Status</option>
                                    <option value="<?= \app\models\Payment::STATUS_PAID ?>" 
                                        <?= Yii::$app->request->get('status') == \app\models\Payment::STATUS_PAID ? 'selected' : '' ?>>
                                        Lunas
                                    </option>
                                    <option value="<?= \app\models\Payment::STATUS_PARTIAL ?>"
                                        <?= Yii::$app->request->get('status') == \app\models\Payment::STATUS_PARTIAL ? 'selected' : '' ?>>
                                        Sebagian
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="glyphicon glyphicon-search"></i> Filter
                        </button>
                        <a href="<?= \yii\helpers\Url::to(['index']) ?>" class="btn btn-default">
                            <i class="glyphicon glyphicon-refresh"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'payment_date',
                'value' => function ($model) {
                    return Yii::$app->formatter->asDate($model->payment_date);
                },
            ],
            [
                'attribute' => 'medical_record_id',
                'label' => 'Pasien',
                'value' => function ($model) {
                    return $model->medicalRecord && $model->medicalRecord->patient
                        ? $model->medicalRecord->patient->name 
                        : '-';
                },
            ],
            [
                'attribute' => 'payment_method',
                'value' => function ($model) {
                    return $model->getPaymentMethodLabel();
                },
            ],
            [
                'attribute' => 'amount',
                'value' => function ($model) {
                    return 'Rp. ' . Yii::$app->formatter->asDecimal($model->amount, 0);
                },
            ],
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    return $model->getStatusLabel();
                },
                'contentOptions' => function ($model) {
                    return $model->status === \app\models\Payment::STATUS_PAID
                        ? ['class' => 'success']
                        : ['class' => 'warning'];
                },
            ],
            [
                'attribute' => 'created_by',
                'value' => function ($model) {
                    return $model->createdBy ? $model->createdBy->username : '-';
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {print}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                            'title' => 'Lihat',
                            'data-pjax' => '0',
                        ]);
                    },
                    'print' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-print"></span>', ['print', 'id' => $model->id], [
                            'title' => 'Cetak Kwitansi',
                            'target' => '_blank',
                            'data-pjax' => '0',
                        ]);
                    },
                ]
            ],
        ],
    ]); ?>

</div>
