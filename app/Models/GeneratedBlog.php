<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GeneratedBlog extends Model
{
    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'user_id', 'domain', 'topic', 'focus_keyword',
        'word_count_target', 'word_count_actual', 'content',
        'status', 'published_at', 'generated_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'generated_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(fn($m) => $m->id = $m->id ?: (string) Str::uuid());
    }
}
