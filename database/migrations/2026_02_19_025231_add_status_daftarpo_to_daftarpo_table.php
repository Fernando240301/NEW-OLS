<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daftarpo', function (Blueprint $table) {
            $table->string('status_daftarpo')
                  ->default('MENUNGGU APPROVAL')
                  ->after('file_penawaran');
        });
    }

    public function down(): void
    {
        Schema::table('daftarpo', function (Blueprint $table) {
            $table->dropColumn('status_daftarpo');
        });
    }
};
