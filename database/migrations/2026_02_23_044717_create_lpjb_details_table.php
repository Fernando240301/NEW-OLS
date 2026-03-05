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
        Schema::create('lpjb_details', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('lpjb_id');
            $table->foreign('lpjb_id')
                ->references('id')
                ->on('lpjbs')
                ->onDelete('cascade');

            $table->unsignedBigInteger('coa_id');
            $table->foreign('coa_id')
                ->references('id')
                ->on('chartofaccounts');

            // budget copy dari PPJB
            $table->decimal('budget_qty', 12, 2)->nullable();
            $table->decimal('budget_harga', 18, 2)->nullable();
            $table->decimal('budget_subtotal', 18, 2)->default(0);

            // realisasi diisi user
            $table->decimal('real_qty', 12, 2)->nullable();
            $table->decimal('real_harga', 18, 2)->nullable();
            $table->decimal('real_subtotal', 18, 2)->default(0);

            $table->string('uraian')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lpjb_details');
    }
};
