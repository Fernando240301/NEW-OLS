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
        Schema::table('ppjbnews', function (Blueprint $table) {

            $table->boolean('tax_processed')
                ->default(false)
                ->after('journal_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ppjbnews', function (Blueprint $table) {
            //
        });
    }
};
