<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pemohon')) {
            Schema::create('pemohon', function (Blueprint $table) {
                $table->id('pemohonid');
                $table->string('nik')->nullable();
                $table->string('nama_perusahaan');
                $table->unsignedBigInteger('klasifikasi');
                $table->string('email_pemohon')->nullable();
                $table->text('alamat_perusahaan')->nullable();
                $table->string('kota_perusahaan')->nullable();
                $table->string('provinsi_perusahaan')->nullable();
                $table->string('negara')->nullable();
                $table->string('kode_pos')->nullable();
                $table->string('telp_perusahaan')->nullable();
                $table->string('contact1')->nullable();
                $table->string('contact_celluler1')->nullable();
                $table->string('contact2')->nullable();
                $table->string('contact_celluler2')->nullable();
                $table->string('contact3')->nullable();
                $table->string('contact_celluler3')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // Jangan drop di server / production
    }
};
