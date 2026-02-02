<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class jenisperalatanseeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::table('jenis_peralatan')->insert([
            [
                'nama_peralatan' => 'Pressure Vessel',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_peralatan' => 'Pressure Safety Valve/Katup Pengaman',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_peralatan' => 'Storage Tank/Tangki Penimbun',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_peralatan' => 'Rotating Equipment/Peralatan Putar',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_peralatan' => 'Electrical/Peralatan Listik',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_peralatan' => 'Custody Transfer/Alat Ukur Serah Terima',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_peralatan' => 'Platform',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_peralatan' => 'Pipeline Installation/Instalasi Pipa Penyalur',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_peralatan' => 'Installation/Instalasi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_peralatan' => 'Lifting Gear Inspection',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_peralatan' => 'Non Destructive Test/NDT',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_peralatan' => 'RIG Installation / Instalasi RIG',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_peralatan' => 'Boiler',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_peralatan' => 'Lighting Protection (Depnaker)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            ]);
    }
    
}
