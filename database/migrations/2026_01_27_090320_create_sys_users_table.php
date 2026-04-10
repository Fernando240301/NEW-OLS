<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Baseline migration
        // Table sys_users already exists (legacy)
    }

    public function down(): void
    {
        // Jangan drop table legacy di server
        // Schema::dropIfExists('sys_users');
    }
};
