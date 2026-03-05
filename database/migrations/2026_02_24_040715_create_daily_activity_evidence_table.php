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
    Schema::create('dailyactivityevidences', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('dailyactivity_id');
        $table->string('log_activity')->nullable();
        $table->string('link')->nullable();
        $table->string('file_path')->nullable();
        $table->timestamps();

        $table->foreign('dailyactivity_id')
              ->references('id')
              ->on('dailyactivities')
              ->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_activity_evidence');
    }
};
