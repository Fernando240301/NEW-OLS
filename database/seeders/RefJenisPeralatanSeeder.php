<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RefJenisPeralatanSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('ref_jenis_peralatan')->insert([
            ['nama' => 'Pressure Vessel/Bejana Tekan', 'catatan' => null],
            ['nama' => 'Pressure Safety Valve/Katup Pengaman', 'catatan' => null],
            ['nama' => 'Storage Tank/Tangki Penimbun', 'catatan' => null],
            ['nama' => 'Rotating Equipment/Peralatan Putar', 'catatan' => null],
            ['nama' => 'Electrical/Peralatan Listrik', 'catatan' => null],
            ['nama' => 'Crane/Pesawat Angkat', 'catatan' => null],
            ['nama' => 'Custody Transfer/Alat Ukur Serah Terima', 'catatan' => null],
            ['nama' => 'Platform', 'catatan' => null],
            ['nama' => 'Pipeline Installation/Instalasi Pipa Penyalur', 'catatan' => null],
            ['nama' => 'Installation/Instalasi', 'catatan' => null],
            ['nama' => 'Lifting Gear Inspection', 'catatan' => null],
            ['nama' => 'Non Destructive Test/NDT', 'catatan' => null],
            ['nama' => 'RIG Installation/Instalasi RIG', 'catatan' => null],
            ['nama' => 'Boiler', 'catatan' => null],
            ['nama' => 'Lighting Protection (Depnaker)', 'catatan' => null],
        ]);
    }
}
