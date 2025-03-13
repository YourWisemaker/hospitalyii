<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MedicalRecord */
/* @var $patients array */
/* @var $doctors array */

$this->title = 'Tambah Rekam Medis Baru';
$this->params['breadcrumbs'][] = ['label' => 'Rekam Medis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="medical-record-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'patients' => $patients,
        'doctors' => $doctors,
    ]) ?>

</div>
