<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daftarpo', function (Blueprint $table) {
            $table->string('no_surat')->nullable()->after('namapengaju');
        });
    }

    public function down(): void
    {
        Schema::table('daftarpo', function (Blueprint $table) {
            $table->dropColumn('no_surat');
        });
    }
};
