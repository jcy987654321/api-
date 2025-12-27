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
        Schema::create('friendly_links', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->string('url', 500);
            $table->string('logo', 500)->nullable();
            $table->string('status', 20)->default('pending')->comment('active, pending, rejected');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->index('status');
            $table->index('url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('friendly_links');
    }
};
