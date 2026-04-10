<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chartofaccounts', function (Blueprint $table) {

            $table->boolean('is_system')
                ->default(false)
                ->after('is_active');

            $table->string('normal_balance')
                ->after('is_system');

            $table->decimal('opening_balance', 18, 2)
                ->default(0)
                ->after('normal_balance');
        });
    }

    public function down(): void
    {
        Schema::table('chartofaccounts', function (Blueprint $table) {

            $table->dropColumn([
                'is_system',
                'normal_balance',
                'opening_balance'
            ]);
        });
    }
};
