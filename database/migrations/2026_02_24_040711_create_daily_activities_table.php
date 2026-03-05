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
    Schema::create('dailyactivities', function (Blueprint $table) {
    $table->id();
    $table->unsignedInteger('user_id'); // samakan dengan sys_users.userid
    $table->date('tanggal');
    $table->string('jenis_kegiatan');
    $table->string('project_number');
    $table->text('uraian')->nullable();
    $table->timestamps();

    $table->foreign('user_id')
          ->references('userid')
          ->on('sys_users')
          ->onDelete('cascade');
});
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_activities');
    }
};
