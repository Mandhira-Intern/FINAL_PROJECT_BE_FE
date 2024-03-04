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
        Schema::create('quizzes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title_quiz');
            $table->string('description_quiz');
            $table->dateTime('open_quiz');
            $table->dateTime('close_quiz');
            $table->integer('time_limit');
            $table->integer('attempts_allowed');
            $table->foreignUuid('activity_id')->references('id')->on('activities')->cascadeOnDelete()->cascadeOnUpdate();
            $table->softDeletes($column = 'deleted_at', $precision = 0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
