<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MedicalRecord */
/* @var $patients array */
/* @var $doctors array */

$this->title = 'Ubah Rekam Medis: ' . ($model->patient ? $model->patient->name : 'Pasien');
$this->params['breadcrumbs'][] = ['label' => 'Rekam Medis', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Detail', 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Ubah';
?>
<div class="medical-record-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'patients' => $patients,
        'doctors' => $doctors,
    ]) ?>

</div>
