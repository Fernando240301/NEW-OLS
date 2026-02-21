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
        Schema::create('gl_entries', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('journal_id');
            $table->unsignedBigInteger('account_id');

            $table->date('entry_date');

            $table->decimal('debit', 18, 2)->default(0);
            $table->decimal('credit', 18, 2)->default(0);

            $table->timestamps();

            $table->foreign('journal_id')
                ->references('id')
                ->on('journals')
                ->cascadeOnDelete();

            // 🔥 INI YANG DIPERBAIKI
            $table->foreign('account_id')
                ->references('id')
                ->on('chartofaccounts')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gl_entries');
    }
};
