<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Devis extends Model
{
    protected $table = 'devis';

    protected $fillable = [
        'user_id',
        'client_user_id',
        'title',
        'client_name',
        'order_reference',
        'place',
        'contact',
        'status',
        'processed_at',
        'line_items',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'processed_at' => 'date',
            'line_items' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function clientUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_user_id');
    }
}
