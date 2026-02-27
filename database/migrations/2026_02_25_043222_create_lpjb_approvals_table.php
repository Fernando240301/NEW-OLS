<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lpjb_approvals', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('lpjb_id');
            $table->integer('user_id'); // sesuai sys_users.userid

            $table->string('role'); // PIC, PCC, Manager, Finance, Director
            $table->integer('step_order');

            $table->enum('status', ['pending', 'approved', 'rejected'])
                ->default('pending');

            $table->text('note')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();

            $table->foreign('lpjb_id')
                ->references('id')
                ->on('lpjbs')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('userid')
                ->on('sys_users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lpjb_approvals');
    }
};
