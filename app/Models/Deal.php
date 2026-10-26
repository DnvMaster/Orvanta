<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deal extends Model
{
    protected $fillable = ['pipeline_id', 'pipeline_stage_id', 'user_id', 'title', 'amount', 'currency', 'description'];

    protected function casts(): array
    {
        return [
            'pipeline_id' => 'integer',
            'pipeline_stage_id' => 'integer',
            'user_id' => 'integer',
            'amount' => 'decimal:2'
        ];
    }

    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(Pipeline::class);
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(PipelineStage::class, 'pipeline_stage_id');
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
