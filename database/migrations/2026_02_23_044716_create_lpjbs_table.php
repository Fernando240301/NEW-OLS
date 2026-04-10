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
        Schema::create('lpjbs', function (Blueprint $table) {
            $table->id();

            $table->string('no_lpjb')->unique();

            $table->unsignedBigInteger('ppjb_id');
            $table->foreign('ppjb_id')
                ->references('id')
                ->on('ppjbnews')
                ->onDelete('cascade');

            $table->date('tanggal');

            $table->decimal('total_budget', 18, 2)->default(0);
            $table->decimal('total_realisasi', 18, 2)->default(0);
            $table->decimal('selisih', 18, 2)->default(0);

            $table->string('status')->default('draft');

            $table->unsignedBigInteger('journal_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lpjbs');
    }
};
