<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\TreatmentDetail */
/* @var $medicalRecord app\models\MedicalRecord */
/* @var $treatments array */

$this->title = 'Tambah Tindakan';
$this->params['breadcrumbs'][] = ['label' => 'Rekam Medis', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Detail', 'url' => ['view', 'id' => $medicalRecord->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="treatment-detail-create">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">Informasi Pasien</h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Nomor Pasien:</strong> <?= $medicalRecord->patient->registration_number ?></p>
                    <p><strong>Nama Pasien:</strong> <?= $medicalRecord->patient->name ?></p>
                    <p><strong>Jenis Kelamin:</strong> <?= $medicalRecord->patient->gender === 'L' ? 'Laki-laki' : 'Perempuan' ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Tanggal Perawatan:</strong> <?= Yii::$app->formatter->asDatetime($medicalRecord->treatment_date) ?></p>
                    <p><strong>Dokter:</strong> <?= $medicalRecord->doctor ? $medicalRecord->doctor->name : '-' ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="treatment-detail-form">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'treatment_id')->dropDownList($treatments, [
            'prompt' => '-- Pilih Tindakan --',
            'onchange' => '
                $.get("' . \yii\helpers\Url::toRoute(['get-treatment-price']) . '", {id: $(this).val()}, function(data) {
                    $("#treatmentdetail-price").val(data);
                });
            '
        ]) ?>

        <?= $form->field($model, 'quantity')->textInput(['type' => 'number', 'min' => 1, 'value' => 1]) ?>

        <?= $form->field($model, 'price')->textInput(['readonly' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Simpan', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Batal', ['view', 'id' => $medicalRecord->id], ['class' => 'btn btn-default']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>
