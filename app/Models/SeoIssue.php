<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeoIssue extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id', 'page_id', 'type', 'severity', 'description', 'is_resolved', 'resolved_at',
    ];

    protected $casts = [
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function getSeverityColorAttribute(): string
    {
        return match($this->severity) {
            'critical' => 'red',
            'warning'  => 'yellow',
            default    => 'blue',
        };
    }

    public function getSeverityIconAttribute(): string
    {
        return match($this->severity) {
            'critical' => '🔴',
            'warning'  => '🟡',
            default    => '🔵',
        };
    }
}
