<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Backlink extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id', 'source_url', 'target_url', 'anchor_text',
        'link_type', 'domain_authority', 'page_authority', 'spam_score',
        'is_active', 'first_seen_at', 'last_checked_at',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'first_seen_at'  => 'date',
        'last_checked_at'=> 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function getSourceDomainAttribute(): string
    {
        return parse_url($this->source_url, PHP_URL_HOST) ?? $this->source_url;
    }
}
