<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ref_tipe_peralatan', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('jenis');
            $table->string('jns_ijin');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ref_tipe_layanan');
    }
};
