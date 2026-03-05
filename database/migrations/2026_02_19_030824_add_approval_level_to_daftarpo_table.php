<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daftarpo', function (Blueprint $table) {
            $table->integer('approval_level')
                  ->default(1)
                  ->after('status_daftarpo');

            $table->unsignedBigInteger('approved_by')
                  ->nullable()
                  ->after('approval_level');
        });
    }

    public function down(): void
    {
        Schema::table('daftarpo', function (Blueprint $table) {
            $table->dropColumn(['approval_level', 'approved_by']);
        });
    }
};
