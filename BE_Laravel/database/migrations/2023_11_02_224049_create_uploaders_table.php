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
        Schema::create('uploaders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('uploader_name');
            $table->string('uploader_file');
            $table->dateTime('uploader_time');
            $table->foreignUuid('assignment_id')->references('id')->on('assignments')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('student_id')->references('id')->on('students')->cascadeOnDelete()->cascadeOnUpdate();
            $table->softDeletes($column = 'deleted_at', $precision = 0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uploaders');
    }
};
