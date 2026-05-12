<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seo_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('page_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', [
                'missing_title', 'duplicate_title', 'long_title', 'short_title',
                'missing_meta', 'duplicate_meta', 'long_meta',
                'missing_h1', 'multiple_h1',
                'broken_link', 'redirect_chain', 'missing_alt',
                'slow_page', 'large_page', 'noindex',
                'missing_canonical', 'duplicate_content',
            ]);
            $table->enum('severity', ['critical', 'warning', 'notice'])->default('notice');
            $table->text('description')->nullable();
            $table->boolean('is_resolved')->default(false);
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'is_resolved', 'severity']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_issues');
    }
};
