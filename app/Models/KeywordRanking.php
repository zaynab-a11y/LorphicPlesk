<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KeywordRanking extends Model
{
    use HasFactory;

    protected $fillable = [
        'keyword_id', 'position', 'url', 'estimated_traffic', 'checked_at',
    ];

    protected $casts = [
        'checked_at' => 'date',
    ];

    public function keyword(): BelongsTo
    {
        return $this->belongsTo(Keyword::class);
    }
}
