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
        Schema::create('daftarpo', function (Blueprint $table) {
            $table->id();
            $table->string('namapengaju');
            $table->string('project');
            $table->string('to');
            $table->string('adress');
            $table->date('date');
            $table->string('attention');
            $table->string('shipto');
            $table->date('shipdate');
            $table->string('description');
            $table->string('qty');
            $table->string('unit');
            $table->string('harga');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daftar_p_o_s');
    }
};
