<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteAudit extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id', 'seo_score', 'pages_crawled',
        'issues_critical', 'issues_warning', 'issues_notice',
        'pages_with_errors', 'broken_links', 'avg_load_time', 'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'seo_score'    => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function getScoreColorAttribute(): string
    {
        return match(true) {
            $this->seo_score >= 80 => 'green',
            $this->seo_score >= 60 => 'yellow',
            default                => 'red',
        };
    }
}
