<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\MedicineDetail */
/* @var $medicalRecord app\models\MedicalRecord */
/* @var $medicines array */

$this->title = 'Tambah Obat';
$this->params['breadcrumbs'][] = ['label' => 'Rekam Medis', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Detail', 'url' => ['view', 'id' => $medicalRecord->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="medicine-detail-create">

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

    <div class="medicine-detail-form">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'medicine_id')->dropDownList($medicines, [
            'prompt' => '-- Pilih Obat --',
            'onchange' => '
                $.get("' . \yii\helpers\Url::toRoute(['get-medicine-price']) . '", {id: $(this).val()}, function(data) {
                    $("#medicinedetail-price").val(data.price);
                    $("#stock-info").html("Stok tersedia: " + data.stock);
                    $("#max-quantity").val(data.stock);
                    
                    if (data.stock <= 0) {
                        $("#stock-warning").show();
                        $("#medicinedetail-quantity").attr("max", 0);
                        $("#medicinedetail-quantity").val(0);
                    } else {
                        $("#stock-warning").hide();
                        $("#medicinedetail-quantity").attr("max", data.stock);
                        $("#medicinedetail-quantity").val(1);
                    }
                });
            '
        ]) ?>

        <div id="stock-info" class="help-block"></div>
        <div id="stock-warning" class="alert alert-danger" style="display: none;">
            Stok obat ini tidak tersedia. Silahkan update stok obat terlebih dahulu.
        </div>

        <?= $form->field($model, 'quantity')->textInput([
            'type' => 'number', 
            'min' => 1, 
            'value' => 1,
            'onchange' => 'checkQuantity(this)',
        ]) ?>
        <input type="hidden" id="max-quantity" value="0">

        <?= $form->field($model, 'price')->textInput(['readonly' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Simpan', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Batal', ['view', 'id' => $medicalRecord->id], ['class' => 'btn btn-default']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>

<?php
$js = <<<JS
function checkQuantity(input) {
    var max = parseInt($("#max-quantity").val());
    var val = parseInt($(input).val());
    
    if (val > max) {
        alert("Jumlah obat melebihi stok yang tersedia (" + max + ")");
        $(input).val(max);
    }
}
JS;
$this->registerJs($js);
?>
