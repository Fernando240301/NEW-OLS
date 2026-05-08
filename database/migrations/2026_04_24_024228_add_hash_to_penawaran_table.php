<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penawaran', function (Blueprint $table) {
            $table->string('hash')->unique()->nullable()->after('nosurat');
        });
    }

    public function down(): void
    {
        Schema::table('penawaran', function (Blueprint $table) {
            $table->dropColumn('hash');
        });
    }
};