<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Keyword extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id', 'keyword', 'target_url', 'search_engine',
        'location', 'device', 'search_volume', 'cpc', 'competition',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function rankings(): HasMany
    {
        return $this->hasMany(KeywordRanking::class);
    }

    public function latestRanking(): HasOne
    {
        return $this->hasOne(KeywordRanking::class)->latestOfMany('checked_at');
    }

    public function previousRanking(): HasOne
    {
        return $this->hasOne(KeywordRanking::class)->ofMany(
            ['checked_at' => 'max'],
            fn ($q) => $q->where('checked_at', '<', now()->subDay())
        );
    }
}
