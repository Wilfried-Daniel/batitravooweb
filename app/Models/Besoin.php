<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Besoin extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'budget',
        'start_label',
        'place',
        'description',
        'duration',
        'short_date',
        'image_path',
        'candidature_count',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function candidatures(): HasMany
    {
        return $this->hasMany(Candidature::class);
    }
}
