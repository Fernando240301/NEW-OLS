<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RefKategoriPeralatanSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('ref_kategori_peralatan')->insert([
            ['nama' => 'Certification', 'alias' => 'New Certification', 'catatan' => null],
            ['nama' => 'Re Certification', 'alias' => 'Existing', 'catatan' => null],
            ['nama' => 'NDT', 'alias' => 'NDT', 'catatan' => null],
            ['nama' => 'RLA', 'alias' => 'RLA', 'catatan' => null],
            ['nama' => 'Penelaahan Design', 'alias' => 'Penelaahan Design', 'catatan' => null],
            ['nama' => 'Re engineering', 'alias' => 'Re engineering', 'catatan' => null], 
            ['nama' => 'Repair', 'alias' => 'Repair', 'catatan' => null], 
            ['nama' => 'Persetujuan Laik Operasi', 'alias' => 'Persetujuan Laik Operasi', 'catatan' => null], 
            ['nama' => 'Inspeksi Kelaikan Skid Tank', 'alias' => 'Inspeksi Kelaikan Skid Tank', 'catatan' => null], 
        ]);
    }
}
