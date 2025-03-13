<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Medicine */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="medicine-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'category')->dropDownList($model->getCategoryOptions(), [
                'prompt' => '-- Pilih Kategori --',
            ]) ?>

            <?= $form->field($model, 'unit')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'purchase_price')->textInput(['type' => 'number']) ?>

            <?= $form->field($model, 'sell_price')->textInput(['type' => 'number']) ?>

            <?= $form->field($model, 'stock')->textInput(['type' => 'number', 'min' => 0]) ?>

            <?= $form->field($model, 'min_stock')->textInput(['type' => 'number', 'min' => 0]) ?>
        </div>
    </div>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Simpan', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Batal', ['index'], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
