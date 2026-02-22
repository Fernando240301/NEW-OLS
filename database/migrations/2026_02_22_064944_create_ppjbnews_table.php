<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('ppjbnews', function (Blueprint $table) {
        $table->id();
        $table->string('no_ppjb')->unique();
        $table->string('dari');
        $table->date('tanggal_permohonan');
        $table->string('project_no')->nullable();
        $table->string('pekerjaan');
        $table->string('pic')->nullable();
        $table->date('tanggal_dibutuhkan');
        $table->foreignId('kas_account_id')
              ->constrained('chartofaccounts');
        $table->string('status')->default('draft');
        $table->foreignId('journal_id')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ppjbnews');
    }
};
