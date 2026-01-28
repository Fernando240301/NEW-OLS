<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Baseline migration
        // Table ref_klasifikasi_client already exists (imported from legacy DB)
    }

    public function down(): void
    {
        Schema::dropIfExists('ref_klasifikasi_client');
    }
};
