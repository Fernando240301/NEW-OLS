<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RefJnsLayananSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('ref_jns_layanan')->insert([
            ['nama_layanan' => 'New Certification', 'alias' => 'New'],
            ['nama_layanan' => 'Extend', 'alias' => 'Extend'],
            ['nama_layanan' => 'Existing', 'alias' => 'Existing'],
            ['nama_layanan' => 'NDT', 'alias' => 'NDT'],
            ['nama_layanan' => 'Magnetic Particle Inspection', 'alias' => 'Magnetic Particle Inspection'],
            ['nama_layanan' => 'Ultrasonic Inspection', 'alias' => 'Ultrasonic'],
            ['nama_layanan' => 'Depenetrant Inspection', 'alias' => 'Depenetrant'],
            ['nama_layanan' => 'Spherical Tank', 'alias' => 'Existing'],
            ['nama_layanan' => 'Complete Inspection', 'alias' => 'Complete Inspection'],
            ['nama_layanan' => 'Pig Launcher Receiver', 'alias' => 'Existing'],
            ['nama_layanan' => 'Surat Permohonan Pemeriksaan Keselamatan Pipa Penyalur', 'alias' => 'PKPP'],
            ['nama_layanan' => 'Annual Inspection', 'alias' => 'AI'],
            ['nama_layanan' => 'Review Document', 'alias' => 'RD'],
            ['nama_layanan' => 'Tangki Ukur', 'alias' => 'TU'],
            ['nama_layanan' => 'Jembatan Timbang', 'alias' => 'JT'],
        ]);
    }
}
