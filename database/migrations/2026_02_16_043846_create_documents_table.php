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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('workflowid');
            $table->unsignedBigInteger('parent_id')->nullable(); // folder parent
            $table->string('name');
            $table->string('type'); // folder | file
            $table->string('file_path')->nullable();
            $table->string('mime_type')->nullable();
            $table->string('uploaded_by')->nullable();
            $table->timestamps();

            $table->foreign('workflowid')->references('workflowid')->on('app_workflow')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
