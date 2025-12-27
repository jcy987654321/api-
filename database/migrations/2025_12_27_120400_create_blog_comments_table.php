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
        Schema::create('blog_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id');
            $table->string('author', 100);
            $table->string('email', 100);
            $table->text('content');
            $table->boolean('approved')->default(false);
            $table->timestamp('created_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('post_id')
                  ->references('id')
                  ->on('blog_posts')
                  ->onDelete('cascade');
            $table->index(['post_id', 'approved']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_comments');
    }
};
