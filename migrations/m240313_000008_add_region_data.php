<?php

use yii\db\Migration;

class m240313_000008_add_region_data extends Migration
{
    public function safeUp()
    {
        $this->batchInsert('{{%region}}', ['name', 'created_at', 'updated_at'], [
            ['Aceh', time(), time()],
            ['Sumatera Utara', time(), time()],
            ['Sumatera Barat', time(), time()],
            ['Riau', time(), time()],
            ['Jambi', time(), time()],
            ['Sumatera Selatan', time(), time()],
            ['Bengkulu', time(), time()],
            ['Lampung', time(), time()],
            ['DKI Jakarta', time(), time()],
            ['Jawa Barat', time(), time()],
            ['Jawa Tengah', time(), time()],
            ['DI Yogyakarta', time(), time()],
            ['Jawa Timur', time(), time()],
            ['Bali', time(), time()],
            ['Nusa Tenggara Barat', time(), time()],
            ['Nusa Tenggara Timur', time(), time()],
            ['Kalimantan Barat', time(), time()],
            ['Kalimantan Tengah', time(), time()],
            ['Kalimantan Selatan', time(), time()],
            ['Kalimantan Timur', time(), time()],
            ['Sulawesi Utara', time(), time()],
            ['Sulawesi Tengah', time(), time()],
            ['Sulawesi Selatan', time(), time()],
            ['Sulawesi Tenggara', time(), time()],
            ['Maluku', time(), time()],
            ['Papua', time(), time()],
        ]);
    }

    public function safeDown()
    {
        $this->delete('{{%region}}');
    }
}