<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{
    public const STATUS_OPEN = 'open';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_RESOLVED = 'resolved';

    public const STATUS_CLOSED = 'closed';

    public const PRIORITY_LOW = 'low';

    public const PRIORITY_NORMAL = 'normal';

    public const PRIORITY_HIGH = 'high';

    /**
     * Options priorité — même valeurs que la validation API / création de ticket.
     *
     * @return list<array{value: string, label: string}>
     */
    public static function prioritySelectOptions(): array
    {
        return [
            ['value' => '', 'label' => 'Normale (défaut)'],
            ['value' => self::PRIORITY_LOW, 'label' => 'Basse'],
            ['value' => self::PRIORITY_NORMAL, 'label' => 'Normale'],
            ['value' => self::PRIORITY_HIGH, 'label' => 'Haute'],
        ];
    }

    /**
     * @return list<string>
     */
    public static function priorityRuleValues(): array
    {
        return [
            self::PRIORITY_LOW,
            self::PRIORITY_NORMAL,
            self::PRIORITY_HIGH,
        ];
    }

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'subject',
        'status',
        'priority',
        'assigned_to_user_id',
        'closed_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'closed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SupportTicketMessage::class)->orderBy('created_at');
    }
}
