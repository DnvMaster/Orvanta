<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Department::class, 'parent_id');
    }

    public function descendants(): HasMany
    {
        return $this->children()->with('descendants');
    }

    public function descendantIds(): array
    {
        $this->loadMissing('descendants');
        $ids = [];
        $collect = function ($departments) use (&$collect, &$ids): void {
            foreach ($departments as $department) {
                $ids[] = $department->id;
                $collect($department->descendants);
            }
        };
        $collect($this->descendants);

        return $ids;
    }
}
