<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generated_blogs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('status')->default('done');
            $table->string('user_id')->nullable();
            $table->string('domain');
            $table->string('topic');
            $table->string('focus_keyword');
            $table->integer('word_count_target')->default(2000);
            $table->integer('word_count_actual')->nullable();
            $table->longText('content')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generated_blogs');
    }
};
