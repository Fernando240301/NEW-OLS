<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {

            // Composite index untuk folder query
            $table->index(['workflowid', 'parent_id'], 'documents_workflow_parent_index');

            // Index untuk sorting folder/file
            $table->index('type', 'documents_type_index');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {

            $table->dropIndex('documents_workflow_parent_index');
            $table->dropIndex('documents_type_index');
        });
    }
};
