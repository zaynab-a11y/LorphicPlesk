<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id', 'url', 'title', 'meta_description', 'h1',
        'word_count', 'http_status', 'load_time_ms',
        'mobile_score', 'desktop_score',
        'is_indexed', 'has_canonical', 'canonical_url', 'last_crawled_at',
    ];

    protected $casts = [
        'is_indexed'     => 'boolean',
        'has_canonical'  => 'boolean',
        'last_crawled_at'=> 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function seoIssues(): HasMany
    {
        return $this->hasMany(SeoIssue::class);
    }

    public function getStatusColorAttribute(): string
    {
        return match(true) {
            $this->http_status >= 500 => 'red',
            $this->http_status >= 400 => 'orange',
            $this->http_status >= 300 => 'yellow',
            default                   => 'green',
        };
    }
}
