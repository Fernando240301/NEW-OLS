<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class typeperalatanseeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      DB::table('type_peralatan')->insert([
            [
                'type' => 'Pressure Vessel',
                'id_peralatan' => '1',
                
            ],
            [
                'type' => 'Heat Exchanger (Shell and Tube)',
                'id_peralatan' => '1',
            ],
            [
                'type' => 'Heat Exchanger (Air Cooler Exchanger)',
                'id_peralatan' => '1',
            ],
            [
                'type' => 'Pressure Safety Valve',
                'id_peralatan' => '2',
            ],
            [
                'type' => 'Pressure Relief Valve',
                'id_peralatan' => '2',
            ],
            [
                'type' => 'Breather Valve',
                'id_peralatan' => '2',
            ],
            [
                'type' => 'Storage Tank Rectangular',
                'id_peralatan' => '3',
            ],
            [
                'type' => 'Pump',
                'id_peralatan' => '4',
            ],
            [
                'type' => 'Compressor',
                'id_peralatan' => '4',
            ],
            [
                'type' => 'Generator',
                'id_peralatan' => '5',
            ],
            [
                'type' => 'Transformer',
                'id_peralatan' => '5',
            ],
            [
                'type' => 'Switchgear',
                'id_peralatan' => '5',
            ],
            [
                'type' => 'MCC',
                'id_peralatan' => '5',
            ],
            [
                'type' => 'Pedestal Crane',
                'id_peralatan' => '6',
            ],
            [
                'type' => 'Mobile Crane',
                'id_peralatan' => '6',
            ],
            [
                'type' => 'Overhead Crane',
                'id_peralatan' => '6',
            ],
            [
                'type' => 'Platform',
                'id_peralatan' => '8',
            ],
            [
                'type' => 'FSO',
                'id_peralatan' => '8',
            ],
            [
                'type' => 'Custody Transfer',
                'id_peralatan' => '7',
            ],
            [
                'type' => 'Storage Tank Roof',
                'id_peralatan' => '3',
            ],
            [
                'type' => 'Carbon Steel Liquid On Shore',
                'id_peralatan' => '9',
            ],
            [
                'type' => 'Carbon Steel Liquid Off Shore',
                'id_peralatan' => '9',
            ],
            [
                'type' => 'Carbon Steel Gas On Shore',
                'id_peralatan' => '9',
            ],
            [
                'type' => 'Carbon Steel Gas Off Shore',
                'id_peralatan' => '9',
            ],
            [
                'type' => 'Polyethylene',
                'id_peralatan' => '9',
            ],
            [
                'type' => 'Installation/Instalasi Hilir',
                'id_peralatan' => '10',
            ],
            [
                'type' => 'Lifting Gear',
                'id_peralatan' => '6',
            ],
            [
                'type' => 'NDT',
                'id_peralatan' => '12',
            ],
            [
                'type' => 'Shackle',
                'id_peralatan' => '11',
            ],
            [
                'type' => 'Spreader Bar',
                'id_peralatan' => '11',
            ],
            [
                'type' => 'Wire Rope Sling',
                'id_peralatan' => '11',
            ],
            [
                'type' => 'Chainblock',
                'id_peralatan' => '11',
            ],
            [
                'type' => 'Lever Block ',
                'id_peralatan' => '11',
            ],
            [
                'type' => 'Installation/Instalasi SPBU',
                'id_peralatan' => '10',
            ],
            [
                'type' => 'Installation/Instalasi Hulu',
                'id_peralatan' => '10',
            ],
            [
                'type' => 'Installation/Instalasi Kilang',
                'id_peralatan' => '10',
            ],
            [
                'type' => 'Instalasi RIG',
                'id_peralatan' => '13',
            ],
            [
                'type' => 'Boiler',
                'id_peralatan' => '14',
            ],
            [
                'type' => 'Jib Cranes',
                'id_peralatan' => '11',
            ],
            [
                'type' => 'Davits',
                'id_peralatan' => '11',
            ],
            [
                'type' => 'Winches',
                'id_peralatan' => '11',
            ],
            [
                'type' => 'LoadTest',
                'id_peralatan' => '11',
            ],
            [
                'type' => 'any type of Cranes',
                'id_peralatan' => '11',
            ],
            [
                'type' => 'Dye Penetrant Inspection',
                'id_peralatan' => '12',
            ],
            [
                'type' => 'Magnetic Particle Inspection',
                'id_peralatan' => '12',
            ],
            [
                'type' => 'Positive Material Identification',
                'id_peralatan' => '12',
            ],
            [
                'type' => 'UT-thickness/welding',
                'id_peralatan' => '12',
            ],
            [
                'type' => 'Eddy Current',
                'id_peralatan' => '12',
            ],
            [
                'type' => 'Runway',
                'id_peralatan' => '11',
            ],
            [
                'type' => 'Monorail Beams',
                'id_peralatan' => '11',
            ],
            [
                'type' => 'Trolley Beams',
                'id_peralatan' => '11',
            ],
            [
                'type' => 'Single Hooks',
                'id_peralatan' => '11',
            ],
            [
                'type' => 'Beam Clamps',
                'id_peralatan' => '11',
            ],
            [
                'type' => 'Pins',
                'id_peralatan' => '11',
            ],
            [
                'type' => 'Master Links',
                'id_peralatan' => '11',
            ],
            [
                'type' => 'Eye Bolts',
                'id_peralatan' => '11',
            ],
            [
                'type' => 'Turnbuckle (Eye-eye)',
                'id_peralatan' => '11',
            ],
            [
                'type' => 'Slings',
                'id_peralatan' => '11',
            ],
            [
                'type' => 'Lashing',
                'id_peralatan' => '11',
            ],
            [
                'type' => 'Chain blocks',
                'id_peralatan' => '11',
            ],
            [
                'type' => 'Pullers',
                'id_peralatan' => '11',
            ],
            [
                'type' => 'Lever Hoist',
                'id_peralatan' => '11',
            ],
            [
                'type' => 'Storage Tank Horizontal',
                'id_peralatan' => '3',
            ],
            [
                'type' => 'Turbin Meter',
                'id_peralatan' => '7',
            ],
            [
                'type' => 'Tangki Ukur Terapung',
              'id_peralatan' => '7',
            ],
            [
                'type' => 'Trunkline',
                'id_peralatan' => '9',
            ],
            [
                'type' => 'Flowline',
                'id_peralatan' => '9',
            ],
            [
                'type' => 'UPS',
                'id_peralatan' => '5',
            ],
            [
                'type' => 'Surge Relief Valve',
                'id_peralatan' => '2',
            ],
            [
                'type' => 'Lighting Protection (Depnaker)',
                'id_peralatan' => '15',
            ],

            ]);
    }
}
