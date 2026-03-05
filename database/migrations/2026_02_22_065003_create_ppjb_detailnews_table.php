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
        Schema::create('ppjb_detailnews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ppjb_id')
                ->constrained('ppjbnews')
                ->cascadeOnDelete();
            $table->foreignId('coa_id')
                ->constrained('chartofaccounts');
            $table->integer('qty');
            $table->string('satuan');
            $table->string('uraian');
            $table->decimal('harga', 18, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ppjb_detailnews');
    }
};
