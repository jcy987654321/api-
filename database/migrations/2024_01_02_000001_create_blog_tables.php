<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index('slug');
        });

        Schema::create('blog_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->timestamps();
            
            $table->index('slug');
        });

        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('slug', 255)->unique();
            $table->text('content');
            $table->text('excerpt')->nullable();
            $table->string('cover_image', 500)->nullable();
            $table->foreignId('category_id')->constrained('blog_categories')->onDelete('set null');
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->integer('views')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('slug');
            $table->index('status');
            $table->index('category_id');
            $table->index(['status', 'created_at']);
        });

        Schema::create('blog_tag', function (Blueprint $table) {
            $table->foreignId('blog_id')->constrained('blogs')->onDelete('cascade');
            $table->foreignId('tag_id')->constrained('blog_tags')->onDelete('cascade');
            $table->primary(['blog_id', 'tag_id']);

            $table->index('tag_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_tag');
        Schema::dropIfExists('blogs');
        Schema::dropIfExists('blog_tags');
        Schema::dropIfExists('blog_categories');
    }
};
