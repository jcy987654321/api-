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
        Schema::create('plugin_hooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plugin_id')->constrained('plugins')->onDelete('cascade');
            $table->string('hook_name', 100);
            $table->string('function_name', 100);
            $table->timestamp('created_at')->useCurrent();

            $table->index(['plugin_id', 'hook_name']);
            $table->unique(['plugin_id', 'hook_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plugin_hooks');
    }
};
