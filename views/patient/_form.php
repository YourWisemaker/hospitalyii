<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Patient */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="patient-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'registration_number')->textInput(['maxlength' => true, 'readonly' => !$model->isNewRecord]) ?>

            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'gender')->dropDownList([
                'M' => 'Laki-laki',
                'F' => 'Perempuan',
            ], ['prompt' => '-- Pilih Jenis Kelamin --']) ?>

            <?= $form->field($model, 'birth_date')->input('date') ?>
            
            <?= $form->field($model, 'registration_date')->input('date', ['value' => $model->registration_date ?? date('Y-m-d')]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'contact')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'address')->textarea(['rows' => 6]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Simpan', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Batal', ['index'], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
