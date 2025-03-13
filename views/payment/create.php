<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Payment */

$this->title = 'Tambah Pembayaran';
$this->params['breadcrumbs'][] = ['label' => 'Daftar Pembayaran', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Form Pembayaran</h3>
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
