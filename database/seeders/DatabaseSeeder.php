<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //Klasifikasi Client
        $this->call([
            RefKlasifikasiClientSeeder::class,
        ]);
        // User::factory(10)->create();

        //Referensi Jenis Peralatan
        $this->call([
            RefJenisPeralatanSeeder::class,
        ]);

        //Referensi Kategori Peralatan
        $this->call([
            RefKategoriPeralatanSeeder::class,
        ]);

        //Referensi Jenis Ijin
        $this->call([
            RefJnsIjinSeeder::class,
        ]);

        //Referensi Jenis Layanan
        $this->call([
            RefJnsLayananSeeder::class,
        ]);

    //  $this->call([
    //     typeperalatanseeder::class,
    //     ]);
    //  $this->call([
    //     jenisperalatanseeder::class,
    // ]);
    }
}
