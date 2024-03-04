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
        Schema::table('courses', function (Blueprint $table) {
            $table->after('studyProgram_id', function (Blueprint $table) {
                $table->foreignUuid('lecture_id')->references('id')->on('lectures')->cascadeOnDelete()->cascadeOnUpdate();
            });
        });

        Schema::table('schedules', function (Blueprint $table) {
            $table->after('end_time', function (Blueprint $table) {
                $table->foreignUuid('course_id')->references('id')->on('courses')->unique()->cascadeOnDelete()->cascadeOnUpdate();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lecture_id_course__f_k');
    }
};
