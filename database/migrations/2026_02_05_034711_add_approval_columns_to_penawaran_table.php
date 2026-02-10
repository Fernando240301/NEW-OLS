<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penawaran', function (Blueprint $table) {
            // Cek kolom barcode sebelum menambahkan
            if (!Schema::hasColumn('penawaran', 'barcode')) {
                $table->string('barcode')->nullable()->after('surat');
            }

            if (!Schema::hasColumn('penawaran', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('barcode');
            }

            if (!Schema::hasColumn('penawaran', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }

            if (!Schema::hasColumn('penawaran', 'pdf')) {
                $table->string('pdf')->nullable()->after('surat');
            }
        });
    }

    public function down(): void
    {
        Schema::table('penawaran', function (Blueprint $table) {
            if (Schema::hasColumn('penawaran', 'barcode')) {
                $table->dropColumn('barcode');
            }
            if (Schema::hasColumn('penawaran', 'approved_by')) {
                $table->dropColumn('approved_by');
            }
            if (Schema::hasColumn('penawaran', 'approved_at')) {
                $table->dropColumn('approved_at');
            }
            if (Schema::hasColumn('penawaran', 'pdf')) {
                $table->dropColumn('pdf');
            }
        });
    }
};
