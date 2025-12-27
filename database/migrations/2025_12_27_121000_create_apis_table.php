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
        Schema::create('apis', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->string('method', 10)->default('GET')->comment('GET, POST, PUT, DELETE, PATCH, etc.');
            $table->string('endpoint', 255)->unique();
            $table->string('file_path', 255)->nullable();
            $table->json('parameters')->nullable();
            $table->string('status', 20)->default('active')->comment('active, inactive, deprecated');
            $table->unsignedBigInteger('call_count')->default(0);
            $table->timestamp('last_called_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('method');
            $table->index(['status', 'method']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apis');
    }
};
