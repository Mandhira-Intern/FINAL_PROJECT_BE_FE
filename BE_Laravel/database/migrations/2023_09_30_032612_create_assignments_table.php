<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migration
     */
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name_assignment');
            $table->string('description');
            $table->string('file_assignment');
            $table->string('type_assignment');
            $table->dateTime('allow_submission');
            $table->dateTime('due_date');
            $table->dateTime('cut_off');
            $table->dateTime('remind_grade');
            $table->integer('max_file');
            $table->integer('max_size');
            $table->foreignUuid('activity_id')->references('id')->on('activities')->cascadeOnDelete()->cascadeOnUpdate();
            $table->softDeletes($column = 'deleted_at', $precision = 0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
