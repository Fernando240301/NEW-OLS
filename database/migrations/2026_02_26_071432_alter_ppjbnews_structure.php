<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ppjbnews', function (Blueprint $table) {

            // 🔥 HAPUS KOLOM LAMA
            $table->dropColumn([
                'project_no',
                'refer_project',
                'tanggal_dibutuhkan',
            ]);

            // 🔥 TAMBAH KOLOM BARU
            $table->unsignedBigInteger('workflow_id')->nullable()->after('no_ppjb');
            $table->unsignedBigInteger('pr_workflow_id')->nullable()->after('workflow_id');
            $table->string('jenis_pengajuan', 50)->after('pr_workflow_id');

            $table->date('tanggal_mulai')->nullable()->after('tanggal_permohonan');
            $table->date('tanggal_selesai')->nullable()->after('tanggal_mulai');
        });
    }

    public function down(): void
    {
        Schema::table('ppjbnews', function (Blueprint $table) {

            // 🔥 HAPUS KOLOM BARU
            $table->dropColumn([
                'workflow_id',
                'pr_workflow_id',
                'jenis_pengajuan',
                'tanggal_mulai',
                'tanggal_selesai',
            ]);

            // 🔥 KEMBALIKAN KOLOM LAMA
            $table->string('project_no')->nullable();
            $table->string('refer_project')->nullable();
            $table->date('tanggal_dibutuhkan');
        });
    }
};
