<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lov_jenis_peralatan', function (Blueprint $table) {

            // 1. Primary Key
            $table->increments('id'); // int(11) AUTO_INCREMENT

            // 2. Relasi ke app_workflow
            $table->unsignedBigInteger('workflowid')->nullable();

            // 3. Data scope
            $table->string('lokasi', 255);
            $table->string('item', 255)->nullable(); // meskipun nanti tidak dipakai
            $table->unsignedInteger('jenis')->nullable(); // FK ke lov_jenis_peralatan
            $table->unsignedInteger('tipe');            // FK ke ref_tipe_peralatan
            $table->unsignedInteger('kategori');        // FK ke kategori (kalau ada)
            $table->unsignedInteger('jumlah');
            $table->string('harga', 255)->nullable();

            // optional (aman untuk audit)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lov_jenis_peralatan');
    }
};
