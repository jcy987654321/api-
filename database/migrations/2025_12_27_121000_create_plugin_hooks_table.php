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
            $table->unsignedBigInteger('plugin_id');
            $table->string('hook_name', 100);
            $table->string('function_name', 100);
            $table->timestamp('created_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('plugin_id')
                  ->references('id')
                  ->on('plugins')
                  ->onDelete('cascade');
            $table->index(['plugin_id', 'hook_name']);
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
