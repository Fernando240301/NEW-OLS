<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    // Schema::table('penawaran', function (Blueprint $table) {
    //     $table->string('pdf')->nullable()->after('approved_at');
    // });
}

public function down()
{
    // Schema::table('penawaran', function (Blueprint $table) {
    //     $table->dropColumn('pdf');
    // });
}

};
