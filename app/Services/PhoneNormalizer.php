<?php

declare(strict_types=1);

namespace App\Services;

class PhoneNormalizer
{
    public function normalize(?string $phone): ?string
    {
        if ($phone === null || trim($phone) === '') {
            return null;
        }
        $digits = preg_replace('/\D+/', '', $phone);
        if ($digits === null || $digits === '') {
            return null;
        }
        // Беларусь: +375XXXXXXXXX.
        if (strlen($digits) === 12 && str_starts_with($digits, '375')) {
            return $digits;
        }
        // Беларусь: 80XXXXXXXXX.
        // Проверяем до российского префикса 8.
        if (strlen($digits) === 11 && str_starts_with($digits, '80')) {
            return '375'.substr($digits, 2);
        }
        // Беларусь: 0XXXXXXXXX.
        if (strlen($digits) === 10 && str_starts_with($digits, '0')) {
            return '375'.substr($digits, 1);
        }
        // Россия: +7XXXXXXXXXX.
        if (strlen($digits) === 11 && str_starts_with($digits, '7')) {
            return $digits;
        }
        // Россия: 8XXXXXXXXXX.
        if (strlen($digits) === 11 && str_starts_with($digits, '8')) {
            return '7'.substr($digits, 1);
        }
        // Россия: XXXXXXXXXX.
        if (strlen($digits) === 10 && str_starts_with($digits, '9')) {
            return '7'.$digits;
        }

        return null;
    }
}
