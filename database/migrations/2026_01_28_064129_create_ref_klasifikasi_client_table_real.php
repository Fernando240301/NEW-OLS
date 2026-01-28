<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ref_klasifikasi_client')) {
            Schema::create('ref_klasifikasi_client', function (Blueprint $table) {
                $table->id();
                $table->string('nama');
                $table->string('catatan')->nullable();
                $table->integer('ord')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // jangan drop di production
    }
};
