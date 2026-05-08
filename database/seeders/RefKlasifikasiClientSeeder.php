<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RefKlasifikasiClientSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('ref_klasifikasi_client')->delete();

        DB::table('ref_klasifikasi_client')->insert([
            ['id' => 1,  'nama' => 'Lain-lain',                     'catatan' => null, 'ord' => 99],
            ['id' => 2,  'nama' => 'KKKS',                          'catatan' => null, 'ord' => 1],
            ['id' => 3,  'nama' => 'Kontraktor',                    'catatan' => null, 'ord' => 2],
            ['id' => 4,  'nama' => 'Owner',                         'catatan' => null, 'ord' => 3],
            ['id' => 5,  'nama' => 'Pertamina Hulu / JOB Hulu',     'catatan' => null, 'ord' => 4],
            ['id' => 6,  'nama' => 'Pertamina Hilir / JOB Hilir',   'catatan' => null, 'ord' => 5],
            ['id' => 7,  'nama' => 'SPBBE - SPBE',                  'catatan' => null, 'ord' => 6],
            ['id' => 8,  'nama' => 'Manufaktur - Fabrikator',       'catatan' => null, 'ord' => 8],
            ['id' => 9,  'nama' => 'BUMN - BUMD',                   'catatan' => null, 'ord' => 9],
            ['id' => 10, 'nama' => 'Prospek',                       'catatan' => null, 'ord' => 10],
            ['id' => 11, 'nama' => 'SPBU - SPBG',                   'catatan' => null, 'ord' => 7],
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
