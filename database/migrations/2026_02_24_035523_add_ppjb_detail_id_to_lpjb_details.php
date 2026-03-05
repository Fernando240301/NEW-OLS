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
        Schema::table('lpjb_details', function (Blueprint $table) {
            $table->unsignedBigInteger('ppjb_detail_id')->nullable()->after('lpjb_id');

            $table->foreign('ppjb_detail_id')
                ->references('id')
                ->on('ppjb_detailnews')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('lpjb_details', function (Blueprint $table) {
            $table->dropForeign(['ppjb_detail_id']);
            $table->dropColumn('ppjb_detail_id');
        });
    }
};
