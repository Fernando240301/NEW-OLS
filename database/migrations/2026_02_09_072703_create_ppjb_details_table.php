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
        Schema::create('ppjb_details', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('ppjb_id');
    $table->integer('qty')->nullable();
    $table->string('satuan', 50)->nullable();
    $table->string('uraian')->nullable();
    $table->decimal('harga', 15, 2)->nullable();
    $table->decimal('total', 15, 2)->nullable();
    $table->string('keterangan')->nullable();
    $table->timestamps();

    $table->foreign('ppjb_id')
          ->references('id')
          ->on('PPJB')
          ->onDelete('cascade');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ppjb_details');
    }
};
