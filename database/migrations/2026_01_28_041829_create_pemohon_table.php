<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Baseline migration
        // Table pemohon already exists (imported from legacy DB)
    }

    public function down(): void
    {
        Schema::dropIfExists('pemohon');
    }
};
