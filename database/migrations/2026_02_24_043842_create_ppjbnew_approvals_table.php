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
        Schema::create('ppjbnew_approvals', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('ppjb_id');
            $table->integer('user_id'); // SESUAI sys_users.userid

            $table->string('role');
            $table->integer('step_order');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('note')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();

            $table->foreign('ppjb_id')
                ->references('id')
                ->on('ppjbnews')
                ->onDelete('cascade');

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
        Schema::dropIfExists('ppjbnew_approvals');
    }
};
