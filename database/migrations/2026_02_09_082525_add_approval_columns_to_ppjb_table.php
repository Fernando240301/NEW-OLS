<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddApprovalColumnsToPpjbTable extends Migration
{
    public function up()
    {
        Schema::table('ppjb', function (Blueprint $table) {

            if (!Schema::hasColumn('ppjb', 'status')) {
                $table->string('status')->default('DRAFT');
            }

            if (!Schema::hasColumn('ppjb', 'approval_level')) {
                $table->integer('approval_level')->default(0);
            }

            if (!Schema::hasColumn('ppjb', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable();
            }

        });
    }

    public function down()
    {
        Schema::table('ppjb', function (Blueprint $table) {

            if (Schema::hasColumn('ppjb', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('ppjb', 'approval_level')) {
                $table->dropColumn('approval_level');
            }

            if (Schema::hasColumn('ppjb', 'approved_by')) {
                $table->dropColumn('approved_by');
            }

        });
    }
}
