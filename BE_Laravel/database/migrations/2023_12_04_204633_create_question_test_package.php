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
        Schema::create('question_test_packages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('question_id')->references('id')->on('questions')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('testPackage_id')->references('id')->on('test_packages')->cascadeOnDelete()->cascadeOnUpdate();
            $table->softDeletes($column = 'deleted_at', $precision = 0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_test_package');
    }
};
