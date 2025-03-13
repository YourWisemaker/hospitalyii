<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Treatment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="treatment-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'code')->textInput([
                'maxlength' => true, 
                'placeholder' => 'Contoh: TDK001',
                'aria-describedby' => 'codeHelpBlock'
            ])->hint('Kode tindakan harus unik dan tidak boleh sama dengan kode tindakan lainnya.', ['id' => 'codeHelpBlock']) ?>

            <?= $form->field($model, 'name')->textInput([
                'maxlength' => true,
                'placeholder' => 'Masukkan nama tindakan',
                'aria-describedby' => 'nameHelpBlock'
            ])->hint('Nama tindakan harus unik dan tidak boleh sama dengan nama tindakan lainnya.', ['id' => 'nameHelpBlock']) ?>

            <?= $form->field($model, 'category')->dropDownList($model->getCategoryOptions(), [
                'prompt' => '-- Pilih Kategori --',
            ]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'price')->textInput(['type' => 'number', 'min' => 0]) ?>

            <?= $form->field($model, 'status')->checkbox() ?>
        </div>
    </div>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Simpan', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Batal', ['index'], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
