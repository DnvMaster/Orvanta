<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    /**
     * Поля, разрешённые для массового заполнения.
     *
     * @var list<string>
     */
    protected $fillable = ['name', 'legal_name', 'tax_number', 'email', 'phone', 'website', 'address', 'notes'];
}
