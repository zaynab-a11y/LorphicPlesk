<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keywords', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('keyword');
            $table->string('target_url')->nullable();
            $table->string('search_engine')->default('google');
            $table->string('location')->default('US');
            $table->string('device')->default('desktop');
            $table->unsignedSmallInteger('search_volume')->nullable();
            $table->decimal('cpc', 8, 2)->nullable();
            $table->decimal('competition', 5, 4)->nullable();
            $table->timestamps();

            $table->unique(['project_id', 'keyword', 'search_engine', 'location', 'device']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keywords');
    }
};
