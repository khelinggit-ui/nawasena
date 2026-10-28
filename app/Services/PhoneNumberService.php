<?php

namespace App\Services;

class PhoneNumberService
{
    /**
     * Normalisasi nomor HP Indonesia ke format 62xxxxxxxxxx.
     * Mengembalikan null jika nomor tidak valid.
     */
    public function normalize(?string $number): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $number);

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '0062')) {
            $digits = substr($digits, 2);
        }

        if (str_starts_with($digits, '620')) {
            $digits = '62'.substr($digits, 3);
        } elseif (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        } elseif (str_starts_with($digits, '8')) {
            $digits = '62'.$digits;
        }

        if (! preg_match('/^62[0-9]{8,13}$/', $digits)) {
            return null;
        }

        return $digits;
    }

    public function isValid(?string $number): bool
    {
        return $this->normalize($number) !== null;
    }
}
