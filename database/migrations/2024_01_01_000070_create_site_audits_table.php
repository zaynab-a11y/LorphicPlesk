<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->decimal('seo_score', 5, 2)->default(0);
            $table->unsignedSmallInteger('pages_crawled')->default(0);
            $table->unsignedSmallInteger('issues_critical')->default(0);
            $table->unsignedSmallInteger('issues_warning')->default(0);
            $table->unsignedSmallInteger('issues_notice')->default(0);
            $table->unsignedSmallInteger('pages_with_errors')->default(0);
            $table->unsignedSmallInteger('broken_links')->default(0);
            $table->decimal('avg_load_time', 8, 2)->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_audits');
    }
};
