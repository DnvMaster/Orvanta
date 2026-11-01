<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasResponsibilityScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasResponsibilityScope;

    /**
     * Поля, разрешённые для массового заполнения.
     *
     * @var list<string>
     */
    protected $fillable = ['name', 'country_code', 'legal_name', 'tax_number', 'tax_number_type', 'email', 'phone', 'website', 'address', 'address_country_code', 'notes'];

    /**
     * Контакты, относящиеся к компании.
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
