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
        Schema::table('journal_audits', function (Blueprint $table) {
            $table->integer('user_id')->nullable()->change();
        });

        Schema::table('journal_audits', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('userid')
                ->on('sys_users')
                ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('journal_audits', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
    }
};
