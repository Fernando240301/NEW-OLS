<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('PPJB', function (Blueprint $table) {
            $table->id();
            $table->string('dari');
            $table->date('tanggal_permohonan');
            $table->date('tanggal_dibutuhkan');
            $table->string('project');
            $table->string('pekerjaan');
            $table->string('PIC');
            $table->string('lokasi_project');
            $table->string('transport');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('PPJB');
    }
};
