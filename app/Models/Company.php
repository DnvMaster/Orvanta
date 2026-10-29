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
    protected $fillable = ['name','legal_name','tax_number','email','phone','website','address','notes'];

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