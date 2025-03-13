<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Employee */
/* @var $form yii\widgets\ActiveForm */
/* @var $regions array */
?>

<div class="employee-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'employee_number')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'position')->dropDownList($model->getPositionOptions(), [
                'prompt' => '-- Pilih Jabatan --',
            ]) ?>

            <?= $form->field($model, 'status')->dropDownList($model->getStatusOptions(), ['prompt' => '-- Pilih Status --']) ?>

            <?= $form->field($model, 'gender')->dropDownList($model->getGenderOptions(), ['prompt' => '-- Pilih Jenis Kelamin --']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'birth_date')->input('date') ?>

            <?= $form->field($model, 'region_id')->dropDownList($regions, [
                'prompt' => '-- Pilih Wilayah --',
            ]) ?>
        </div>
    </div>

    <?= $form->field($model, 'address')->textarea(['rows' => 4]) ?>

    <div class="form-group">
        <?= Html::submitButton('Simpan', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Batal', ['index'], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
