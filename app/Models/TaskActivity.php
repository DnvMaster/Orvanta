<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

class TaskActivity extends Model
{
    public $timestamps = false;

    protected $fillable = ['task_id', 'user_id', 'event_type', 'old_values', 'new_values', 'metadata'];

    protected function casts(): array
    {
        return ['old_values' => 'array', 'new_values' => 'array', 'metadata' => 'array', 'created_at' => 'datetime'];
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted(): void
    {
        static::updating(function (TaskActivity $activity): void {
            throw new LogicException('История задач не подлежит изменению.');
        });

        static::deleting(function (TaskActivity $activity): void {
            throw new LogicException('История задач не подлежит удалению.');
        });
    }
}
