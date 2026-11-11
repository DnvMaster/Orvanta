<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\PhoneNormalizer;
use PHPUnit\Framework\TestCase;

class PhoneNormalizerTest extends TestCase
{
    public function test_it_normalizes_russian_phone_numbers(): void
    {
        $normalizer = new PhoneNormalizer;
        $cases = [
            ['+7 999 123-45-67', '79991234567'],
            ['89991234567', '79991234567'],
            ['9991234567', '79991234567'],
        ];

        foreach ($cases as [$phone, $expected]) {
            $this->assertSame($expected, $normalizer->normalize($phone));
        }
    }

    public function test_it_normalizes_belarusian_phone_numbers(): void
    {
        $normalizer = new PhoneNormalizer;
        $cases = [
            ['+375 29 123-45-67', '375291234567'],
            ['8 029 123-45-67', '375291234567'],
            ['029 123-45-67', '375291234567'],
        ];

        foreach ($cases as [$phone, $expected]) {
            $this->assertSame($expected, $normalizer->normalize($phone));
        }
    }

    public function test_it_returns_null_for_empty_or_unsupported_numbers(): void
    {
        $normalizer = new PhoneNormalizer;
        $cases = [null, '', '   ', '+1 202 555 0123', '+49 30 123456', '12345', 'abcdefgh'];
        foreach ($cases as $phone) {
            $this->assertNull($normalizer->normalize($phone));
        }
    }
}
