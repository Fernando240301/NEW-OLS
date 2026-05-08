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
    Schema::table('ppjb', function (Blueprint $table) {
        $table->text('nosurat')->nullable()->before('dari');
    });
}

public function down(): void
{
    Schema::table('ppjb', function (Blueprint $table) {
        $table->dropColumn('nosurat');
    });
}

};
