<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_workflow_deleted', function (Blueprint $table) {

            $table->bigIncrements('workflowid');

            $table->integer('codeid')->nullable();
            $table->string('projectname')->nullable();
            $table->string('tipe')->nullable();
            $table->string('resi')->nullable();
            $table->bigInteger('client')->nullable();

            $table->string('processname')->nullable();
            $table->string('processcategory')->nullable();

            $table->string('createuser')->nullable();
            $table->dateTime('createtime')->nullable();

            $table->longText('workflowdata')->nullable();

            $table->string('next_taskname')->nullable();
            $table->string('next_stepname')->nullable();
            $table->string('next_rolename')->nullable();
            $table->string('next_status')->nullable();

            $table->dateTime('last_update')->nullable();
            $table->string('last_username')->nullable();
            $table->string('last_status')->nullable();

            $table->bigInteger('nworkflowid')->nullable();
            $table->string('flag')->nullable();
            $table->string('userflag')->nullable();
            $table->dateTime('dateflag')->nullable();

            $table->string('kode')->nullable();
            $table->string('device')->nullable();

            $table->string('jns_ijin')->nullable();
            $table->string('jns_layanan')->nullable();

            $table->string('noreg')->nullable();
            $table->string('nib')->nullable();

            $table->text('note')->nullable();

            $table->string('apv_mm')->nullable();
            $table->dateTime('date_mm')->nullable();
            $table->string('apv_mo')->nullable();
            $table->dateTime('date_mo')->nullable();
            $table->string('apv_mf')->nullable();
            $table->dateTime('date_mf')->nullable();

            $table->string('closing_project')->nullable();
            $table->string('downpayment')->nullable();
            $table->text('notefinance')->nullable();

            // 🔥 tambahan khusus deleted
            $table->dateTime('deleted_at')->nullable();
            $table->string('deleted_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_workflow_deleted');
    }
};
