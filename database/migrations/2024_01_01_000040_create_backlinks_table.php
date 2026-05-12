<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('backlinks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('source_url');
            $table->string('target_url');
            $table->string('anchor_text')->nullable();
            $table->enum('link_type', ['dofollow', 'nofollow', 'ugc', 'sponsored'])->default('dofollow');
            $table->unsignedSmallInteger('domain_authority')->nullable();
            $table->unsignedSmallInteger('page_authority')->nullable();
            $table->unsignedSmallInteger('spam_score')->nullable();
            $table->boolean('is_active')->default(true);
            $table->date('first_seen_at')->nullable();
            $table->date('last_checked_at')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backlinks');
    }
};
