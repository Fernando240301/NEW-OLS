<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ref_jns_ijin', function (Blueprint $table) {

            // Primary Key (char(3))
            $table->char('jns_ijin', 3)->primary();

            // Kolom lainnya
            $table->string('nama_jns_ijin', 255)->nullable();
            $table->char('kode', 10);

            $table->enum('tipe', ['pr', 'jns_unit', 'unit', 'doc', 'ga'])
                ->default('unit');

            $table->integer('masa_tahun')->nullable();
            $table->integer('masa_bulan')->nullable();

            // int(2) di MySQL = integer biasa (display width)
            $table->integer('retribusi')->nullable();

            $table->integer('bidang')->nullable();

            // aktif & oss wajib (No, None)
            $table->integer('aktif');
            $table->integer('oss');

            $table->integer('order_index')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ref_jns_ijin');
    }
};