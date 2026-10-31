<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
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
}
