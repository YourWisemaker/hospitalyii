<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Payment */
/* @var $medicalRecord app\models\MedicalRecord */

$this->title = 'Proses Pembayaran';
$this->params['breadcrumbs'][] = ['label' => 'Rekam Medis', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Detail', 'url' => ['view', 'id' => $medicalRecord->id]];
$this->params['breadcrumbs'][] = $this->title;

$totalAmount = $medicalRecord->getTotalAmount();
$paidAmount = $medicalRecord->getPaidAmount();
$remainingAmount = $totalAmount - $paidAmount;
?>
<div class="payment-create">

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

    <div class="panel panel-warning">
        <div class="panel-heading">
            <h3 class="panel-title">Rincian Biaya</h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <h4>Detail Tindakan</h4>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tindakan</th>
                                <th>Jumlah</th>
                                <th>Harga</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($medicalRecord->treatmentDetails as $detail): ?>
                            <tr>
                                <td><?= $detail->treatment ? $detail->treatment->name : 'Tidak tersedia' ?></td>
                                <td><?= $detail->quantity ?></td>
                                <td>Rp. <?= Yii::$app->formatter->asDecimal($detail->price, 0) ?></td>
                                <td>Rp. <?= Yii::$app->formatter->asDecimal($detail->getSubtotal(), 0) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <tr class="success">
                                <th colspan="3">Total Biaya Tindakan</th>
                                <th>Rp. <?= Yii::$app->formatter->asDecimal($medicalRecord->getTreatmentTotal(), 0) ?></th>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <h4>Detail Obat</h4>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Obat</th>
                                <th>Jumlah</th>
                                <th>Harga</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($medicalRecord->medicineDetails as $detail): ?>
                            <tr>
                                <td><?= $detail->medicine ? $detail->medicine->name : 'Tidak tersedia' ?></td>
                                <td><?= $detail->quantity ?></td>
                                <td>Rp. <?= Yii::$app->formatter->asDecimal($detail->price, 0) ?></td>
                                <td>Rp. <?= Yii::$app->formatter->asDecimal($detail->getSubtotal(), 0) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <tr class="success">
                                <th colspan="3">Total Biaya Obat</th>
                                <th>Rp. <?= Yii::$app->formatter->asDecimal($medicalRecord->getMedicineTotal(), 0) ?></th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 col-md-offset-6">
                    <table class="table table-bordered">
                        <tr class="info">
                            <th>Total Keseluruhan</th>
                            <td>Rp. <?= Yii::$app->formatter->asDecimal($totalAmount, 0) ?></td>
                        </tr>
                        <tr>
                            <th>Jumlah Telah Dibayar</th>
                            <td>Rp. <?= Yii::$app->formatter->asDecimal($paidAmount, 0) ?></td>
                        </tr>
                        <tr class="<?= $remainingAmount > 0 ? 'danger' : 'success' ?>">
                            <th>Sisa Pembayaran</th>
                            <td>Rp. <?= Yii::$app->formatter->asDecimal($remainingAmount, 0) ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php if ($remainingAmount > 0): ?>
        <div class="payment-form">
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h3 class="panel-title">Form Pembayaran</h3>
                </div>
                <div class="panel-body">
                    <?php $form = ActiveForm::begin(); ?>

                    <?= $form->field($model, 'medical_record_id')->hiddenInput(['value' => $medicalRecord->id])->label(false) ?>

                    <?= $form->field($model, 'payment_date')->input('date', [
                        'value' => date('Y-m-d'),
                    ]) ?>

                    <?= $form->field($model, 'payment_method')->dropDownList($model->getPaymentMethodOptions(), [
                        'prompt' => '-- Pilih Metode Pembayaran --',
                    ]) ?>

                    <div class="form-group field-payment-payment-method-details">
                        <label class="control-label" for="payment-payment-method-details">Detail Pembayaran</label>
                        <input type="text" id="payment-payment-method-details" class="form-control" name="Payment[payment_method_details]" placeholder="Contoh: Nomor Kartu, Referensi, dll">
                        <div class="help-block"></div>
                    </div>

                    <?= $form->field($model, 'amount')->textInput([
                        'type' => 'number',
                        'step' => '1000',
                        'min' => '1000',
                        'max' => $remainingAmount,
                        'value' => $remainingAmount,
                    ]) ?>

                    <?= $form->field($model, 'notes')->textarea(['rows' => 3]) ?>

                    <div class="form-group">
                        <?= Html::submitButton('Proses Pembayaran', ['class' => 'btn btn-success']) ?>
                        <?= Html::a('Batal', ['view', 'id' => $medicalRecord->id], ['class' => 'btn btn-default']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-success">
            <h4><i class="glyphicon glyphicon-ok"></i> Pembayaran Telah Lunas</h4>
            <p>Semua biaya pada rekam medis ini telah dibayar lunas.</p>
            <p>
                <?= Html::a('Kembali ke Rekam Medis', ['view', 'id' => $medicalRecord->id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Cetak Kwitansi', ['print-receipt', 'id' => $medicalRecord->id], ['class' => 'btn btn-warning', 'target' => '_blank']) ?>
            </p>
        </div>
    <?php endif; ?>

</div>
