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
        Schema::create('api_parameters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_id')->constrained('apis')->onDelete('cascade');
            $table->string('name', 100);
            $table->string('type', 50)->default('string');
            $table->boolean('required')->default(false);
            $table->text('description')->nullable();
            $table->timestamp('created_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index(['api_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_parameters');
    }
};
