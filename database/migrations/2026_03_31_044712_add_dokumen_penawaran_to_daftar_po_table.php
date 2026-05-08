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
    Schema::table('daftar_po', function (Blueprint $table) {
        $table->string('dokumen_penawaran')->nullable()->after('description');
    });
}

public function down(): void
{
    Schema::table('daftar_po', function (Blueprint $table) {
        $table->dropColumn('dokumen_penawaran');
    });
}
};
