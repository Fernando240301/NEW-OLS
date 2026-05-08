<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::table('ppjbnews', function (Blueprint $table) {

            if (!Schema::hasColumn('ppjbnews', 'refer_project')) {
                $table->string('refer_project')->nullable();
            }

            if (!Schema::hasColumn('ppjbnews', 'total')) {
                $table->decimal('total', 18, 2)->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('ppjbnews', function (Blueprint $table) {

            if (Schema::hasColumn('ppjbnews', 'refer_project')) {
                $table->dropColumn('refer_project');
            }

            if (Schema::hasColumn('ppjbnews', 'total')) {
                $table->dropColumn('total');
            }
        });
    }
};
