<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Payment */

$this->title = 'Ubah Pembayaran: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Daftar Pembayaran', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Ubah';
?>
<div class="payment-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Form Ubah Pembayaran</h3>
        </div>
        <div class="panel-body">
            <?= $this->render('_form', [
                'model' => $model,
                'medicalRecordOptions' => $medicalRecordOptions,
                'paymentMethods' => $paymentMethods,
            ]) ?>
        </div>
    </div>

</div>
