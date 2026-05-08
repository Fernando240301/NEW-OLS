<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('penawaran', function (Blueprint $table) {

            // DROP FK lama
            // $table->dropForeign(['approved_by']);

            // SAMAKAN TIPE KOLUMN DULU
            // $table->unsignedBigInteger('approved_by')->nullable()->change();

            // BUAT FK BARU KE sys_users.userid
            // $table->foreign('approved_by')
            //       ->references('userid')
            //       ->on('sys_users')
            //       ->nullOnDelete();
        });
    }

    public function down(): void
    {
    //     Schema::table('penawaran', function (Blueprint $table) {

    //         $table->dropForeign(['approved_by']);

    //         // BALIK KE users.id
    //         $table->foreign('approved_by')
    //               ->references('id')
    //               ->on('users')
    //               ->nullOnDelete();
    //     });
     }
};
