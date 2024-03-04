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
        Schema::create('attendances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kategory');
            $table->string('info');
            $table->dateTime('time_arrive');
            $table->dateTime('time_leave');
            $table->foreignUuid('course_id')->references('id')->on('courses')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('student_id')->references('id')->on('students')->cascadeOnDelete()->cascadeOnUpdate();
            $table->softDeletes($column = 'deleted_at', $precision = 0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
