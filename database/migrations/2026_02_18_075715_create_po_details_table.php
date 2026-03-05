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
    Schema::create('po_details', function (Blueprint $table) {
        $table->id();

        $table->foreignId('po_id')
              ->constrained('daftarpo')
              ->onDelete('cascade');

        $table->string('item_name')->nullable();
        $table->string('qty')->nullable();
        $table->string('unit')->nullable();
        $table->decimal('price', 15, 2)->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('po_details');
    }
};
