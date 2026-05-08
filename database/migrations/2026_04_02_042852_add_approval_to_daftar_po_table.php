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
    Schema::table('daftar_po', function (Blueprint $table) {
    $table->string('marketing_status')->default('pending');
    $table->string('finance_status')->default('pending');
    $table->string('direktur_status')->default('pending');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daftar_po', function (Blueprint $table) {
            //
        });
    }
};
