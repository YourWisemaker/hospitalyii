<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Treatment */

$this->title = 'Ubah Tindakan: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Daftar Tindakan', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Ubah';
?>
<div class="treatment-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
