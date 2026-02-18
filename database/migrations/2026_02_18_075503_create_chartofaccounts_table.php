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
        Schema::create('chartofaccounts', function (Blueprint $table) {
            $table->id();

            $table->string('code')->unique(); // 1101-001-02
            $table->string('name');

            $table->foreignId('account_type_id')
                ->constrained('account_types')
                ->cascadeOnDelete();

            $table->foreignId('account_category_id')
                ->nullable()
                ->constrained('account_categories')
                ->nullOnDelete();

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('chartofaccounts')
                ->cascadeOnDelete();

            $table->integer('level')->default(1); // depth tree
            $table->boolean('is_active')->default(true);
            $table->boolean('is_postable')->default(false); // hanya leaf bisa true

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chartofaccounts');
    }
};
