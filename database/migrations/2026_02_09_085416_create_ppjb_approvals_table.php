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
    if (!Schema::hasTable('ppjb_approvals')) {
    Schema::create('ppjb_approvals', function (Blueprint $table) {
        $table->id();
        $table->foreignId('ppjb_id')->constrained('PPJB')->onDelete('cascade');
        $table->unsignedBigInteger('user_id');
        $table->integer('level');
        $table->boolean('is_approved')->default(false);
        $table->timestamp('approved_at')->nullable();
        $table->timestamps();
    });
    }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ppjb_approvals');
    }
};
