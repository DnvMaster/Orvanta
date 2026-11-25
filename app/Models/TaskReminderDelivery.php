<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskReminderDelivery extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'task_id',
        'recipient_id',
        'reminder_type',
        'due_at_snapshot',
        'scheduled_for',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'due_at_snapshot' => 'datetime',
            'scheduled_for' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
}
