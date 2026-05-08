<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('daftar_po', function (Blueprint $table) {
            $table->id();

            // Header PO
            $table->string('nama_pengaju');
            $table->string('pr_number')->unique();
            $table->string('to');
            $table->text('address');
            $table->date('date');
            $table->string('attention')->nullable();
            $table->string('ship_to');
            $table->date('ship_date')->nullable();

            // Detail item
            $table->text('description');
            $table->integer('qty');
            $table->string('unit');
            $table->decimal('unit_price', 15, 2);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('daftar_po');
    }
};