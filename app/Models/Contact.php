<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    /**
     * Поля, разрешённые для массового заполнения.
     *
     * @var list<string>
     */
    protected $fillable = ['company_id','last_name','first_name','middle_name','email','phone','position','notes'];

    /**
     * Компания, к которой относится контакт.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
    /**
     * Сделки, связанные с контактом.
    */
    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }
}
