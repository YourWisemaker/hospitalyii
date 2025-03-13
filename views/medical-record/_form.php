<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\MedicalRecord */
/* @var $form yii\widgets\ActiveForm */
/* @var $patients array */
/* @var $doctors array */
?>

<div class="medical-record-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'patient_id')->dropDownList($patients, [
                'prompt' => '-- Pilih Pasien --',
                'disabled' => !$model->isNewRecord,
            ]) ?>

            <?= $form->field($model, 'doctor_id')->dropDownList($doctors, [
                'prompt' => '-- Pilih Dokter --',
            ]) ?>

            <?= $form->field($model, 'treatment_date')->input('datetime-local', [
                'value' => date('Y-m-d\TH:i', strtotime($model->treatment_date)),
            ]) ?>

            <?= $form->field($model, 'status')->dropDownList($model->getStatusOptions(), [
                'disabled' => $model->status === \app\models\MedicalRecord::STATUS_COMPLETED,
            ]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'complaint')->textarea(['rows' => 6]) ?>

            <?= $form->field($model, 'diagnosis')->textarea(['rows' => 6]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Simpan', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Batal', ['index'], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
