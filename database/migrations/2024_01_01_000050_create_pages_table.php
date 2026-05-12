<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('url');
            $table->string('title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('h1')->nullable();
            $table->unsignedSmallInteger('word_count')->nullable();
            $table->unsignedSmallInteger('http_status')->nullable();
            $table->unsignedSmallInteger('load_time_ms')->nullable();
            $table->decimal('mobile_score', 5, 2)->nullable();
            $table->decimal('desktop_score', 5, 2)->nullable();
            $table->boolean('is_indexed')->default(true);
            $table->boolean('has_canonical')->default(false);
            $table->string('canonical_url')->nullable();
            $table->timestamp('last_crawled_at')->nullable();
            $table->timestamps();

            $table->unique(['project_id', 'url']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
