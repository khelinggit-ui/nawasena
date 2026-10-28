<?php

namespace Tests\Unit;

use App\Services\PhoneNumberService;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PhoneNumberServiceTest extends TestCase
{
    #[DataProvider('validNumbers')]
    public function test_it_normalizes_valid_indonesian_numbers(string $input, string $expected): void
    {
        $service = new PhoneNumberService();

        $this->assertSame($expected, $service->normalize($input));
        $this->assertTrue($service->isValid($input));
    }

    #[DataProvider('invalidNumbers')]
    public function test_it_rejects_invalid_numbers(?string $input): void
    {
        $service = new PhoneNumberService();

        $this->assertNull($service->normalize($input));
        $this->assertFalse($service->isValid($input));
    }

    public static function validNumbers(): array
    {
        return [
            'awalan nol' => ['08123456789', '628123456789'],
            'awalan 62' => ['628123456789', '628123456789'],
            'plus 62 spasi strip' => ['+62 812-3456-789', '628123456789'],
            'tanpa awalan' => ['8123456789', '628123456789'],
            'awalan 0062' => ['0062 812 3456 789', '628123456789'],
            'awalan 620' => ['6208123456789', '628123456789'],
        ];
    }

    public static function invalidNumbers(): array
    {
        return [
            'kosong' => [''],
            'null' => [null],
            'huruf' => ['abc'],
            'terlalu pendek' => ['12345'],
            'terlalu panjang' => ['6281234567890123'],
        ];
    }
}
