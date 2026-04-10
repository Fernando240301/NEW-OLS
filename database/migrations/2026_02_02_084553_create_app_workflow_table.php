<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_workflow', function (Blueprint $table) {
            $table->bigIncrements('workflowid');

            $table->integer('codeid')->nullable();

            $table->string('projectname', 500);
            $table->string('tipe', 100)->default('unit');
            $table->string('resi', 20);
            $table->string('client', 50);

            $table->string('processname', 255)->nullable()->index();
            $table->string('processcategory', 255)->nullable()->index();

            $table->string('createuser', 255)->nullable();
            $table->dateTime('createtime')->nullable();

            $table->mediumText('workflowdata')->nullable();

            $table->string('next_taskname', 255)->nullable()->index();
            $table->string('next_stepname', 255)->nullable();
            $table->string('next_rolename', 255)->nullable()->index();
            $table->string('next_status', 255)->nullable();

            $table->dateTime('last_update')->nullable();
            $table->string('last_username', 255)->nullable();
            $table->string('last_status', 255)->nullable();

            $table->integer('nworkflowid')->nullable();

            $table->string('flag', 500)->nullable();
            $table->string('userflag', 100)->nullable();
            $table->dateTime('dateflag')->nullable();

            $table->string('kode', 50)->nullable();
            $table->string('device', 20)->nullable();

            $table->string('jns_ijin', 10);
            $table->string('jns_layanan', 10);

            $table->string('noreg', 32);
            $table->string('nib', 16);

            $table->text('note')->nullable();

            $table->integer('apv_mm')->nullable();
            $table->string('date_mm', 255)->nullable();

            $table->integer('apv_mo')->nullable();
            $table->string('date_mo', 255)->nullable();

            $table->integer('apv_mf')->nullable();
            $table->string('date_mf', 255)->nullable();

            $table->integer('closing_project')->default(0);
            $table->string('downpayment', 11)->default('0');

            $table->text('notefinance')->nullable();

            // Charset & collation mengikuti tabel lama
            $table->charset = 'latin1';
            $table->collation = 'latin1_swedish_ci';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_workflow');
    }
};
