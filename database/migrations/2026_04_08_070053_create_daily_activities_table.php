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
 
    Schema::create('daily_activities', function (Blueprint $table) {
        $table->id();

        // relasi ke user (karena kamu pakai sys_users)
        $table->unsignedBigInteger('user_id');

        // input utama
        $table->date('activity_date');
        $table->string('jenis_kegiatan'); // dropdown
        $table->string('project_number')->nullable();
        $table->text('uraian')->nullable();

        // tambahan dari gambar
        $table->string('link')->nullable();

        // optional (kalau nanti mau upload file)
        $table->string('evidence')->nullable();

        // status
        $table->enum('status', ['draft', 'submit', 'approve', 'reject'])
              ->default('draft');

        $table->timestamps();
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
