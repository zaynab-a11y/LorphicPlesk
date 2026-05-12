<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'domain', 'url', 'status', 'notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function keywords(): HasMany
    {
        return $this->hasMany(Keyword::class);
    }

    public function backlinks(): HasMany
    {
        return $this->hasMany(Backlink::class);
    }

    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }

    public function seoIssues(): HasMany
    {
        return $this->hasMany(SeoIssue::class);
    }

    public function siteAudits(): HasMany
    {
        return $this->hasMany(SiteAudit::class);
    }

    public function latestAudit(): HasOne
    {
        return $this->hasOne(SiteAudit::class)->latestOfMany();
    }

    public function getHealthScoreAttribute(): int
    {
        $audit = $this->latestAudit;
        return $audit ? (int) $audit->seo_score : 0;
    }
}
