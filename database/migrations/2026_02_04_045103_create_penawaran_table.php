<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penawaran', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT

            $table->string('nosurat');
            $table->string('judul')->nullable();
            $table->string('namaclient')->nullable();
            $table->string('pic')->nullable();
            $table->string('picmit');

            $table->date('tanggal')->nullable();
            $table->string('status', 50)->nullable();

            $table->string('surat')->nullable();
            $table->string('barcode')->nullable();

            // TANPA foreign key user
            $table->bigInteger('approved_by')->nullable()
                  ->comment('userid dari sys_users / user external');
            $table->timestamp('approved_at')->nullable();

            $table->decimal('harga', 15, 2)->nullable();

            $table->timestamps(); // created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penawaran');
    }
};
