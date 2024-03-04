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
        Schema::create('scoring_deadlines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type_scroring');
            $table->dateTime('deadline');
            $table->foreignUuid('quiz_id')->references('id')->on('quizzes')->cascadeOnDelete()->cascadeOnUpdate()->nullable();
            $table->foreignUuid('assignment_id')->references('id')->on('assignments')->cascadeOnDelete()->cascadeOnUpdate()->nullable();
            $table->softDeletes($column = 'deleted_at', $precision = 0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scoring_deadline');
    }
};
