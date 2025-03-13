<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Payment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="payment-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'medical_record_id')->dropDownList(
        $medicalRecordOptions,
        ['prompt' => 'Pilih Rekam Medis...']
    ) ?>

    <?= $form->field($model, 'amount')->textInput([
        'type' => 'number',
        'step' => '1000',
        'min' => '0',
        'placeholder' => 'Masukkan jumlah pembayaran'
    ]) ?>

    <?= $form->field($model, 'payment_date')->textInput([
        'type' => 'date',
        'value' => $model->isNewRecord ? date('Y-m-d') : date('Y-m-d', strtotime($model->payment_date))
    ]) ?>

    <?= $form->field($model, 'payment_method')->dropDownList(
        $paymentMethods,
        ['prompt' => 'Pilih Metode Pembayaran...']
    ) ?>

    <?= $form->field($model, 'status')->dropDownList([
        \app\models\Payment::STATUS_PENDING => 'Belum Lunas',
        \app\models\Payment::STATUS_PARTIAL => 'Lunas Sebagian',
        \app\models\Payment::STATUS_PAID => 'Lunas'
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton('Simpan', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Batal', ['index'], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
