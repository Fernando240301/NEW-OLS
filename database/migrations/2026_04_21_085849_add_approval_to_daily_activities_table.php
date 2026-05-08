<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('daily_activities', function (Blueprint $table) {

        if (!Schema::hasColumn('daily_activities', 'status')) {
            $table->enum('status', ['pending', 'approved', 'rejected'])
                ->default('pending')
                ->after('link');
        }

        if (!Schema::hasColumn('daily_activities', 'approved_by')) {
            $table->unsignedBigInteger('approved_by')->nullable()->after('status');
        }

        if (!Schema::hasColumn('daily_activities', 'approved_at')) {
            $table->timestamp('approved_at')->nullable()->after('approved_by');
        }

        if (!Schema::hasColumn('daily_activities', 'rejected_by')) {
            $table->unsignedBigInteger('rejected_by')->nullable()->after('approved_at');
        }

        if (!Schema::hasColumn('daily_activities', 'rejected_at')) {
            $table->timestamp('rejected_at')->nullable()->after('rejected_by');
        }

        });
    }
    public function down(): void
    {
        Schema::table('daily_activities', function (Blueprint $table) {

            $table->dropColumn([
                'status',
                'approved_by',
                'approved_at',
                'rejected_by',
                'rejected_at'
            ]);
        });
    }
};