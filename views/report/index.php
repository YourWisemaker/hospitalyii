<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Laporan';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="report-index">
    <h1><?= Html::encode($this->title) ?></h1>
    
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Laporan Pasien</h3>
                </div>
                <div class="panel-body">
                    <p>Laporan statistik pasien meliputi data demografis, distribusi usia, dan pasien baru.</p>
                    <?= Html::a('<i class="glyphicon glyphicon-stats"></i> Lihat Laporan', ['patient'], ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">Laporan Rekam Medis</h3>
                </div>
                <div class="panel-body">
                    <p>Laporan statistik rekam medis meliputi tren kunjungan pasien dan status pembayaran.</p>
                    <?= Html::a('<i class="glyphicon glyphicon-stats"></i> Lihat Laporan', ['medical-record'], ['class' => 'btn btn-info']) ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h3 class="panel-title">Laporan Pembayaran</h3>
                </div>
                <div class="panel-body">
                    <p>Laporan keuangan meliputi pendapatan harian, bulanan, dan berdasarkan metode pembayaran.</p>
                    <?= Html::a('<i class="glyphicon glyphicon-stats"></i> Lihat Laporan', ['payment'], ['class' => 'btn btn-success']) ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-warning">
                <div class="panel-heading">
                    <h3 class="panel-title">Laporan Obat</h3>
                </div>
                <div class="panel-body">
                    <p>Laporan inventaris obat meliputi stok, nilai aset, dan obat yang sering diresepkan.</p>
                    <?= Html::a('<i class="glyphicon glyphicon-stats"></i> Lihat Laporan', ['medicine'], ['class' => 'btn btn-warning']) ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="panel panel-danger">
                <div class="panel-heading">
                    <h3 class="panel-title">Laporan Tindakan</h3>
                </div>
                <div class="panel-body">
                    <p>Laporan tindakan medis meliputi tindakan yang sering dilakukan dan pendapatan per kategori.</p>
                    <?= Html::a('<i class="glyphicon glyphicon-stats"></i> Lihat Laporan', ['treatment'], ['class' => 'btn btn-danger']) ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Laporan Karyawan</h3>
                </div>
                <div class="panel-body">
                    <p>Laporan karyawan meliputi statistik berdasarkan posisi dan status keaktifan.</p>
                    <?= Html::a('<i class="glyphicon glyphicon-stats"></i> Lihat Laporan', ['employee'], ['class' => 'btn btn-default']) ?>
                </div>
            </div>
        </div>
    </div>
</div>
