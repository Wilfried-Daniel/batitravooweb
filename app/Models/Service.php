<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'description',
        'location',
        'image_url',
        'image_path',
        'service_kind',
        'price_variables',
        'price_fixed_label',
        'rating',
        'review_count',
        'status',
        'is_visible',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'price_variables' => 'boolean',
            'is_visible' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
