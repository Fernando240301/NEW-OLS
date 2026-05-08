<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rpums', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ppjb_id')->constrained('ppjbnews')->cascadeOnDelete();

            $table->date('tanggal_transfer');
            $table->decimal('jumlah', 18, 2);

            $table->string('bukti_transfer')->nullable();

            $table->foreignId('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rpums');
    }
};
