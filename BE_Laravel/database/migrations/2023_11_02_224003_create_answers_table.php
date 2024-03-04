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
        Schema::create('answers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('score');
            $table->string('answer_text');
            $table->foreignUuid('question_id')->references('id')->on('questions')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('quiz_attempt_id')->references('id')->on('quiz_attempts')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('choice_options_id')->references('id')->on('choice_options')->cascadeOnDelete()->cascadeOnUpdate();
            $table->softDeletes($column = 'deleted_at', $precision = 0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};
