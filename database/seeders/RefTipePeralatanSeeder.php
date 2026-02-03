<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RefTipePeralatanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nama' => 'Pressure Vessel', 'jenis' => 1, 'jns_ijin' => '003'],
            ['nama' => 'Heat Exchanger (Shell and Tube)', 'jenis' => 1, 'jns_ijin' => '009'],
            ['nama' => 'Heat Exchanger (Air Cooler Exchanger)', 'jenis' => 1, 'jns_ijin' => '008'],

            ['nama' => 'Pressure Safety Valve', 'jenis' => 2, 'jns_ijin' => '010'],
            ['nama' => 'Pressure Relief Valve', 'jenis' => 2, 'jns_ijin' => '011'],
            ['nama' => 'Breather Valve', 'jenis' => 2, 'jns_ijin' => '012'],
            ['nama' => 'Surge Relief Valve', 'jenis' => 2, 'jns_ijin' => '069'],

            ['nama' => 'Storage Tank Rectangular', 'jenis' => 3, 'jns_ijin' => '013'],
            ['nama' => 'Storage Tank Roof', 'jenis' => 3, 'jns_ijin' => '034'],
            ['nama' => 'Storage Tank Horizontal', 'jenis' => 3, 'jns_ijin' => '063'],

            ['nama' => 'Pump', 'jenis' => 4, 'jns_ijin' => '014'],
            ['nama' => 'Compressor', 'jenis' => 4, 'jns_ijin' => '015'],

            ['nama' => 'Generator', 'jenis' => 5, 'jns_ijin' => '016'],
            ['nama' => 'Transformer', 'jenis' => 5, 'jns_ijin' => '017'],
            ['nama' => 'Switchgear', 'jenis' => 5, 'jns_ijin' => '018'],
            ['nama' => 'MCC', 'jenis' => 5, 'jns_ijin' => '019'],
            ['nama' => 'UPS', 'jenis' => 5, 'jns_ijin' => '068'],

            ['nama' => 'Pedestal Crane', 'jenis' => 6, 'jns_ijin' => '020'],
            ['nama' => 'Mobile Crane', 'jenis' => 6, 'jns_ijin' => '021'],
            ['nama' => 'Overhead Crane', 'jenis' => 6, 'jns_ijin' => '022'],
            ['nama' => 'Lifting Gear', 'jenis' => 6, 'jns_ijin' => '048'],

            ['nama' => 'Custody Transfer', 'jenis' => 7, 'jns_ijin' => '025'],
            ['nama' => 'Turbin Meter', 'jenis' => 7, 'jns_ijin' => '064'],
            ['nama' => 'Tangki Ukur Terapung', 'jenis' => 7, 'jns_ijin' => '065'],

            ['nama' => 'Platform', 'jenis' => 8, 'jns_ijin' => '026'],
            ['nama' => 'FSO', 'jenis' => 8, 'jns_ijin' => '027'],

            ['nama' => 'Carbon Steel Liquid On Shore', 'jenis' => 9, 'jns_ijin' => '029'],
            ['nama' => 'Carbon Steel Liquid Off Shore', 'jenis' => 9, 'jns_ijin' => '031'],
            ['nama' => 'Carbon Steel Gas On Shore', 'jenis' => 9, 'jns_ijin' => '032'],
            ['nama' => 'Carbon Steel Gas Off Shore', 'jenis' => 9, 'jns_ijin' => '033'],
            ['nama' => 'Polyethylene', 'jenis' => 9, 'jns_ijin' => '030'],
            ['nama' => 'Trunkline', 'jenis' => 9, 'jns_ijin' => '066'],
            ['nama' => 'Flowline', 'jenis' => 9, 'jns_ijin' => '067'],

            ['nama' => 'Installation/Instalasi Hilir', 'jenis' => 10, 'jns_ijin' => '028'],
            ['nama' => 'Installation/Instalasi SPBU', 'jenis' => 10, 'jns_ijin' => '050'],
            ['nama' => 'Installation/Instalasi Hulu', 'jenis' => 10, 'jns_ijin' => '051'],
            ['nama' => 'Installation/Instalasi Kilang', 'jenis' => 10, 'jns_ijin' => '052'],

            ['nama' => 'NDT', 'jenis' => 12, 'jns_ijin' => null],
            ['nama' => 'Dye Penetrant Inspection', 'jenis' => 12, 'jns_ijin' => null],
            ['nama' => 'Magnetic Particle Inspection', 'jenis' => 12, 'jns_ijin' => null],
            ['nama' => 'Positive Material Identification', 'jenis' => 12, 'jns_ijin' => null],
            ['nama' => 'UT-thickness/welding', 'jenis' => 12, 'jns_ijin' => null],
            ['nama' => 'Eddy Current', 'jenis' => 12, 'jns_ijin' => null],

            ['nama' => 'Instalasi RIG', 'jenis' => 13, 'jns_ijin' => '061'],
            ['nama' => 'Boiler', 'jenis' => 14, 'jns_ijin' => '062'],
            ['nama' => 'Lighting Protection (Depnaker)', 'jenis' => 15, 'jns_ijin' => '070'],
        ];

        foreach ($data as $row) {
            DB::table('ref_tipe_peralatan')->updateOrInsert(
                [
                    'nama'  => $row['nama'],
                    'jenis' => $row['jenis'],
                ],
                $row
            );
        }
    }
}
