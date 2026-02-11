<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddApprovalColumnsToPpjbTable extends Migration
{
    public function up()
    {
        Schema::table('PPJB', function (Blueprint $table) {

            if (!Schema::hasColumn('PPJB', 'status')) {
                $table->string('status')->default('DRAFT');
            }

            if (!Schema::hasColumn('PPJB', 'approval_level')) {
                $table->integer('approval_level')->default(0);
            }

            if (!Schema::hasColumn('PPJB', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable();
            }

        });
    }

    public function down()
    {
        Schema::table('PPJB', function (Blueprint $table) {

            if (Schema::hasColumn('PPJB', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('PPJB', 'approval_level')) {
                $table->dropColumn('approval_level');
            }

            if (Schema::hasColumn('PPJB', 'approved_by')) {
                $table->dropColumn('approved_by');
            }

        });
    }
}
