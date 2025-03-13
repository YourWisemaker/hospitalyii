<?php
use yii\helpers\Html;

$this->title = 'Dashboard';
?>
<div class="site-index">
    <div class="row">
        <!-- Main Content Area -->
        <div class="col-md-8">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-sm-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Pasien</h5>
                            <h2 class="card-text">4</h2>
                            <?= Html::a('Lihat Semua', ['/patient/index'], ['class' => 'text-white']) ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Karyawan</h5>
                            <h2 class="card-text">0</h2>
                            <?= Html::a('Lihat Semua', ['/employee/index'], ['class' => 'text-white']) ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Rekam Medis</h5>
                            <h2 class="card-text">0</h2>
                            <?= Html::a('Lihat Semua', ['/medical-record/index'], ['class' => 'text-white']) ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Pendapatan</h5>
                            <h2 class="card-text">Rp. (belum diset)</h2>
                            <?= Html::a('Lihat Laporan', ['/report/income'], ['class' => 'text-white']) ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts and other content can go here -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Pasien Perhari (30 Hari Terakhir)</h5>
                </div>
                <div class="card-body">
                    <!-- Your chart content here -->
                </div>
            </div>
        </div>

        <!-- Right Sidebar Menu -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Menu Cepat</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?= Html::a('<i class="fas fa-user-plus"></i> Tambah Pasien Baru', ['/patient/create'], 
                            ['class' => 'list-group-item list-group-item-action d-flex align-items-center']) ?>
                        <?= Html::a('<i class="fas fa-notes-medical"></i> Buat Rekam Medis Baru', ['/medical-record/create'], 
                            ['class' => 'list-group-item list-group-item-action d-flex align-items-center']) ?>
                        <?= Html::a('<i class="fas fa-cash-register"></i> Proses Pembayaran', ['/payment/create'], 
                            ['class' => 'list-group-item list-group-item-action d-flex align-items-center']) ?>
                        <?= Html::a('<i class="fas fa-pills"></i> Kelola Obat', ['/medicine/index'], 
                            ['class' => 'list-group-item list-group-item-action d-flex align-items-center']) ?>
                        <?= Html::a('<i class="fas fa-procedures"></i> Kelola Tindakan', ['/treatment/index'], 
                            ['class' => 'list-group-item list-group-item-action d-flex align-items-center']) ?>
                        <?= Html::a('<i class="fas fa-users"></i> Kelola Karyawan', ['/employee/index'], 
                            ['class' => 'list-group-item list-group-item-action d-flex align-items-center']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
