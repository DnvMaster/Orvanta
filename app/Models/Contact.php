<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasResponsibilityScope;
use App\Services\PhoneNormalizer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    use HasResponsibilityScope;

    protected $fillable = ['company_id', 'last_name', 'first_name', 'middle_name', 'email', 'phone', 'position', 'notes'];

    protected static function booted(): void
    {
        static::saving(function (Contact $contact): void {
            $contact->phone_normalized = app(PhoneNormalizer::class)->normalize($contact->phone);
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }
}
