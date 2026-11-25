<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskAttachment extends Model
{
    use SoftDeletes;

    protected $fillable = ['task_id', 'user_id', 'original_name', 'stored_name', 'disk', 'path', 'mime_type', 'extension', 'size', 'checksum'];

    protected function casts(): array
    {
        return ['size' => 'integer'];
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
