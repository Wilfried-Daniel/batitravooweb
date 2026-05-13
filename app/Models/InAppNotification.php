<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InAppNotification extends Model
{
    /**
     * @var list<string>
     */
    public const TYPE_CANDIDATURE = 'candidature';

    public const TYPE_MESSAGE = 'message';

    public const TYPE_DEVIS = 'devis';

    public const TYPE_SYSTEM = 'system';

    public const TYPE_SUPPORT_TICKET = 'support_ticket';

    protected $table = 'in_app_notifications';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'body',
        'data',
        'read_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data' => 'array',
            'read_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markRead(): void
    {
        if ($this->read_at === null) {
            $this->forceFill(['read_at' => now()])->save();
        }
    }
}
