<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Medicine */

$this->title = 'Update Stok: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Daftar Obat', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update Stok';
?>
<div class="medicine-update-stock">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">Informasi Obat</h3>
                </div>
                <div class="panel-body">
                    <table class="table table-striped">
                        <tr>
                            <th>Kode</th>
                            <td><?= $model->code ?></td>
                        </tr>
                        <tr>
                            <th>Nama</th>
                            <td><?= $model->name ?></td>
                        </tr>
                        <tr>
                            <th>Kategori</th>
                            <td><?= $model->getCategoryLabel() ?></td>
                        </tr>
                        <tr>
                            <th>Satuan</th>
                            <td><?= $model->unit ?></td>
                        </tr>
                        <tr>
                            <th>Stok Saat Ini</th>
                            <td>
                                <span class="label label-<?= $model->stock <= $model->min_stock ? 'danger' : 'success' ?>">
                                    <?= $model->stock ?> <?= $model->unit ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Stok Minimum</th>
                            <td><?= $model->min_stock ?> <?= $model->unit ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Update Stok</h3>
                </div>
                <div class="panel-body">
                    <?php $form = ActiveForm::begin(); ?>

                    <div class="form-group">
                        <label class="control-label">Jenis Update</label>
                        <div class="radio">
                            <label>
                                <input type="radio" name="stockOperation" value="add" checked>
                                Penambahan Stok (Masuk)
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="stockOperation" value="subtract">
                                Pengurangan Stok (Keluar)
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label" for="stock-amount">Jumlah</label>
                        <input type="number" id="stock-amount" class="form-control" name="stockAmount" min="1" value="1">
                        <div class="help-block">Masukkan jumlah yang ingin ditambahkan/dikurangkan</div>
                    </div>

                    <div class="form-group">
                        <label class="control-label" for="stock-notes">Catatan</label>
                        <textarea id="stock-notes" class="form-control" name="stockNotes" rows="3"></textarea>
                        <div class="help-block">Opsional. Masukkan alasan update stok, nomor batch, dll.</div>
                    </div>

                    <div class="form-group">
                        <?= Html::submitButton('Update Stok', ['class' => 'btn btn-success']) ?>
                        <?= Html::a('Batal', ['view', 'id' => $model->id], ['class' => 'btn btn-default']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>

</div>
