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
        Schema::table('app_workflow', function (Blueprint $table) {
            $table->string('apv_token', 100)->nullable()->after('apv_mm');
            $table->dateTime('apv_token_expired')->nullable()->after('apv_token');
        });
    }

    public function down()
    {
        Schema::table('app_workflow', function (Blueprint $table) {
            $table->dropColumn('apv_token');
        });
    }
};
