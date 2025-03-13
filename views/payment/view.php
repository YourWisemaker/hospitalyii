<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Payment */

$this->title = 'Detail Pembayaran #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Daftar Pembayaran', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$medicalRecord = $model->medicalRecord;
$patient = $medicalRecord ? $medicalRecord->patient : null;
?>
<div class="payment-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Cetak Kwitansi', ['print', 'id' => $model->id], [
            'class' => 'btn btn-warning',
            'target' => '_blank',
        ]) ?>
        <?= Html::a('Kembali', ['index'], ['class' => 'btn btn-default']) ?>
    </p>

    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Informasi Pembayaran</h3>
                </div>
                <div class="panel-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                            [
                                'attribute' => 'payment_date',
                                'value' => function ($model) {
                                    return Yii::$app->formatter->asDate($model->payment_date);
                                },
                            ],
                            [
                                'attribute' => 'medical_record_id',
                                'value' => function ($model) {
                                    return $model->medical_record_id;
                                },
                            ],
                            [
                                'attribute' => 'payment_method',
                                'value' => function ($model) {
                                    return $model->getPaymentMethodLabel();
                                },
                            ],
                            'payment_method_details',
                            [
                                'attribute' => 'amount',
                                'value' => function ($model) {
                                    return 'Rp. ' . Yii::$app->formatter->asDecimal($model->amount, 0);
                                },
                            ],
                            [
                                'attribute' => 'status',
                                'value' => function ($model) {
                                    return $model->getStatusLabel();
                                },
                            ],
                            'notes:ntext',
                            [
                                'attribute' => 'created_at',
                                'value' => function ($model) {
                                    return Yii::$app->formatter->asDatetime($model->created_at);
                                },
                            ],
                            [
                                'attribute' => 'created_by',
                                'value' => function ($model) {
                                    return $model->createdBy ? $model->createdBy->username : '-';
                                },
                            ],
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
        
        <?php if ($patient): ?>
        <div class="col-md-6">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">Informasi Pasien</h3>
                </div>
                <div class="panel-body">
                    <table class="table table-striped">
                        <tr>
                            <th>Nomor Pasien</th>
                            <td><?= $patient->registration_number ?></td>
                        </tr>
                        <tr>
                            <th>Nama Pasien</th>
                            <td><?= $patient->name ?></td>
                        </tr>
                        <tr>
                            <th>Jenis Kelamin</th>
                            <td><?= $patient->gender === 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
                        </tr>
                        <tr>
                            <th>Telepon</th>
                            <td><?= $patient->phone ?></td>
                        </tr>
                        <tr>
                            <th>Tanggal Perawatan</th>
                            <td><?= Yii::$app->formatter->asDatetime($medicalRecord->treatment_date) ?></td>
                        </tr>
                        <tr>
                            <th>Dokter</th>
                            <td><?= $medicalRecord->doctor ? $medicalRecord->doctor->name : '-' ?></td>
                        </tr>
                    </table>
                    
                    <p>
                        <?= Html::a('Lihat Detail Rekam Medis', ['medical-record/view', 'id' => $medicalRecord->id], [
                            'class' => 'btn btn-primary btn-sm'
                        ]) ?>
                    </p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <?php if ($medicalRecord): ?>
    <div class="panel panel-success">
        <div class="panel-heading">
            <h3 class="panel-title">Ringkasan Biaya</h3>
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
                            <td>Rp. <?= Yii::$app->formatter->asDecimal($medicalRecord->getTotalAmount(), 0) ?></td>
                        </tr>
                        <tr>
                            <th>Jumlah Telah Dibayar</th>
                            <td>Rp. <?= Yii::$app->formatter->asDecimal($medicalRecord->getPaidAmount(), 0) ?></td>
                        </tr>
                        <tr class="<?= $medicalRecord->getRemainingAmount() > 0 ? 'danger' : 'success' ?>">
                            <th>Sisa Pembayaran</th>
                            <td>Rp. <?= Yii::$app->formatter->asDecimal($medicalRecord->getRemainingAmount(), 0) ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>
