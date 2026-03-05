<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('lpjb_details', function (Blueprint $table) {
            $table->string('bukti_file')->nullable()->after('real_subtotal');
        });
    }

    public function down()
    {
        Schema::table('lpjb_details', function (Blueprint $table) {
            $table->dropColumn('bukti_file');
        });
    }
};
