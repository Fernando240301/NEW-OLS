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
    Schema::create('po_approvals', function (Blueprint $table) {
        $table->id();

        $table->foreignId('po_id')
              ->constrained('daftarpo')
              ->onDelete('cascade');

        $table->unsignedBigInteger('user_id'); 
        // karena kamu pakai auth()->user()->userid
        // sesuaikan dengan tipe di tabel user kamu

        $table->integer('level');
        $table->boolean('is_approved')->default(false);
        $table->timestamp('approved_at')->nullable();

        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('po_approvals');
    }
};
