<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Payment */

$this->title = 'Kwitansi Pembayaran';
$this->params['breadcrumbs'][] = ['label' => 'Pembayaran', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$medicalRecord = $model->medicalRecord;
$patient = $medicalRecord ? $medicalRecord->patient : null;

// Set the response format for printing
$this->registerCss('
    body {
        font-family: Arial, sans-serif;
        font-size: 12pt;
    }
    .receipt-header {
        text-align: center;
        margin-bottom: 20px;
    }
    .receipt-header h1 {
        margin-bottom: 5px;
    }
    .receipt-header p {
        margin: 2px 0;
    }
    .receipt-title {
        text-align: center;
        font-weight: bold;
        font-size: 16pt;
        margin: 15px 0;
        border-bottom: 1px solid #000;
        padding-bottom: 5px;
    }
    .receipt-body {
        margin-bottom: 30px;
    }
    .receipt-section {
        margin-bottom: 20px;
    }
    .receipt-table {
        width: 100%;
        border-collapse: collapse;
        margin: 15px 0;
    }
    .receipt-table th, .receipt-table td {
        border: 1px solid #000;
        padding: 5px;
    }
    .receipt-table th {
        background-color: #f0f0f0;
    }
    .receipt-footer {
        text-align: right;
        margin-top: 30px;
    }
    .amount-in-words {
        font-style: italic;
        margin: 10px 0;
    }
    .signatures {
        margin-top: 50px;
        display: flex;
        justify-content: space-between;
    }
    .signature-box {
        text-align: center;
        width: 40%;
    }
    .signature-line {
        border-top: 1px solid #000;
        margin-top: 50px;
        padding-top: 5px;
    }
    @media print {
        .no-print {
            display: none;
        }
    }
');
?>

<div class="no-print" style="margin-bottom: 15px;">
    <button onclick="window.print()" class="btn btn-primary">
        <i class="glyphicon glyphicon-print"></i> Cetak
    </button>
    <?= Html::a('Kembali', ['view', 'id' => $model->id], ['class' => 'btn btn-default']) ?>
</div>

<div class="receipt-container">
    <div class="receipt-header">
        <h1>KLINIK INOVA MEDIKA</h1>
        <p>Jl. Jendral Sudirman No. 123, Jakarta Selatan</p>
        <p>Telepon: (021) 555-1234 | Email: info@inovamedika.com</p>
    </div>

    <div class="receipt-title">
        KWITANSI PEMBAYARAN
        <br>
        <span style="font-size: 12pt;">No. <?= sprintf('INV/%s/%06d', date('Ymd', strtotime($model->payment_date)), $model->id) ?></span>
    </div>

    <div class="receipt-body">
        <div class="receipt-section">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 20%;">Tanggal</td>
                    <td style="width: 3%;">:</td>
                    <td style="width: 77%;"><?= Yii::$app->formatter->asDate($model->payment_date) ?></td>
                </tr>
                <?php if ($patient): ?>
                <tr>
                    <td>Telah Diterima Dari</td>
                    <td>:</td>
                    <td><?= $patient->name ?></td>
                </tr>
                <tr>
                    <td>No. Pasien</td>
                    <td>:</td>
                    <td><?= $patient->registration_number ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td>Metode Pembayaran</td>
                    <td>:</td>
                    <td><?= $model->getPaymentMethodLabel() ?> <?= $model->payment_method_details ? '(' . $model->payment_method_details . ')' : '' ?></td>
                </tr>
                <tr>
                    <td>Jumlah Pembayaran</td>
                    <td>:</td>
                    <td><strong>Rp. <?= Yii::$app->formatter->asDecimal($model->amount, 0) ?></strong></td>
                </tr>
                <tr>
                    <td>Terbilang</td>
                    <td>:</td>
                    <td class="amount-in-words"><?= ucfirst(Yii::$app->formatter->asCurrency($model->amount, 'IDR')) ?></td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">Untuk Pembayaran</td>
                    <td style="vertical-align: top;">:</td>
                    <td>
                        <?php if ($medicalRecord): ?>
                            Layanan kesehatan pada tanggal <?= Yii::$app->formatter->asDate($medicalRecord->treatment_date) ?>
                            <?= $model->notes ? '<br>' . nl2br(Html::encode($model->notes)) : '' ?>
                        <?php else: ?>
                            <?= $model->notes ? nl2br(Html::encode($model->notes)) : 'Layanan kesehatan' ?>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>

        <?php if ($medicalRecord): ?>
        <div class="receipt-section">
            <h4>Rincian Biaya:</h4>
            
            <?php if (!empty($medicalRecord->treatmentDetails)): ?>
            <div>
                <strong>A. Tindakan</strong>
                <table class="receipt-table">
                    <tr>
                        <th>No</th>
                        <th>Nama Tindakan</th>
                        <th>Jumlah</th>
                        <th>Harga</th>
                        <th>Subtotal</th>
                    </tr>
                    <?php foreach ($medicalRecord->treatmentDetails as $index => $detail): ?>
                    <tr>
                        <td style="text-align: center;"><?= $index + 1 ?></td>
                        <td><?= $detail->treatment ? $detail->treatment->name : 'Tidak tersedia' ?></td>
                        <td style="text-align: center;"><?= $detail->quantity ?></td>
                        <td style="text-align: right;">Rp. <?= Yii::$app->formatter->asDecimal($detail->price, 0) ?></td>
                        <td style="text-align: right;">Rp. <?= Yii::$app->formatter->asDecimal($detail->getSubtotal(), 0) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="4" style="text-align: right;"><strong>Total Tindakan</strong></td>
                        <td style="text-align: right;"><strong>Rp. <?= Yii::$app->formatter->asDecimal($medicalRecord->getTreatmentTotal(), 0) ?></strong></td>
                    </tr>
                </table>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($medicalRecord->medicineDetails)): ?>
            <div>
                <strong>B. Obat-obatan</strong>
                <table class="receipt-table">
                    <tr>
                        <th>No</th>
                        <th>Nama Obat</th>
                        <th>Jumlah</th>
                        <th>Harga</th>
                        <th>Subtotal</th>
                    </tr>
                    <?php foreach ($medicalRecord->medicineDetails as $index => $detail): ?>
                    <tr>
                        <td style="text-align: center;"><?= $index + 1 ?></td>
                        <td><?= $detail->medicine ? $detail->medicine->name : 'Tidak tersedia' ?></td>
                        <td style="text-align: center;"><?= $detail->quantity ?> <?= $detail->medicine ? $detail->medicine->unit : '' ?></td>
                        <td style="text-align: right;">Rp. <?= Yii::$app->formatter->asDecimal($detail->price, 0) ?></td>
                        <td style="text-align: right;">Rp. <?= Yii::$app->formatter->asDecimal($detail->getSubtotal(), 0) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="4" style="text-align: right;"><strong>Total Obat</strong></td>
                        <td style="text-align: right;"><strong>Rp. <?= Yii::$app->formatter->asDecimal($medicalRecord->getMedicineTotal(), 0) ?></strong></td>
                    </tr>
                </table>
            </div>
            <?php endif; ?>
            
            <div style="margin-top: 20px;">
                <table class="receipt-table">
                    <tr>
                        <td style="text-align: right; width: 80%;"><strong>Total Keseluruhan</strong></td>
                        <td style="text-align: right; width: 20%;"><strong>Rp. <?= Yii::$app->formatter->asDecimal($medicalRecord->getTotalAmount(), 0) ?></strong></td>
                    </tr>
                    <tr>
                        <td style="text-align: right;"><strong>Jumlah Telah Dibayar Sebelumnya</strong></td>
                        <td style="text-align: right;">Rp. <?= Yii::$app->formatter->asDecimal($medicalRecord->getPaidAmount() - $model->amount, 0) ?></td>
                    </tr>
                    <tr>
                        <td style="text-align: right;"><strong>Pembayaran Kali Ini</strong></td>
                        <td style="text-align: right;">Rp. <?= Yii::$app->formatter->asDecimal($model->amount, 0) ?></td>
                    </tr>
                    <tr>
                        <td style="text-align: right;"><strong>Sisa Pembayaran</strong></td>
                        <td style="text-align: right;">Rp. <?= Yii::$app->formatter->asDecimal($medicalRecord->getRemainingAmount(), 0) ?></td>
                    </tr>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="receipt-footer">
        <div class="signatures">
            <div class="signature-box">
                <p>Pasien</p>
                <div class="signature-line">
                    <?= $patient ? $patient->name : '________________' ?>
                </div>
            </div>
            
            <div class="signature-box">
                <p>Jakarta, <?= Yii::$app->formatter->asDate($model->payment_date) ?><br>Petugas</p>
                <div class="signature-line">
                    <?= $model->createdBy ? $model->createdBy->username : '________________' ?>
                </div>
            </div>
        </div>
    </div>
</div>
