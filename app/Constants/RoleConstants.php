<?php

namespace App\Constants;

class RoleConstants
{
    // Role IDs - sesuaikan dengan ID di database
    const ADMIN_ID = 1;
    const USER_ID = 2;
    const SPV_ID = 3;
    const ADMINKREDIT_ID = 4;
    const MARKETING_ID = 5;
    const ANALIS_ID = 6;
    const RISK_ID = 7;
    const MITRA_ID = 8;

    // Role Names
    const ADMIN = 'admin';
    const USER = 'user';
    const SPV = 'spv';
    const ADMINKREDIT = 'adminkredit';
    const MARKETING = 'marketing';
    const ANALIS = 'analis';
    const RISK = 'risk';
    const MITRA = 'mitra';

    /**
     * Get all role IDs
     */
    public static function getAllIds(): array
    {
        return [
            self::ADMIN_ID,
            self::USER_ID,
            self::SPV_ID,
            self::ADMINKREDIT_ID,
            self::MARKETING_ID,
            self::ANALIS_ID,
            self::RISK_ID,
            self::MITRA_ID,
        ];
    }

    /**
     * Get role name by ID
     */
    public static function getNameById(int $id): ?string
    {
        return match($id) {
            self::ADMIN_ID => self::ADMIN,
            self::USER_ID => self::USER,
            self::SPV_ID => self::SPV,
            self::ADMINKREDIT_ID => self::ADMINKREDIT,
            self::MARKETING_ID => self::MARKETING,
            self::ANALIS_ID => self::ANALIS,
            self::RISK_ID => self::RISK,
            self::MITRA_ID => self::MITRA,
            default => null,
        };
    }

    /**
     * Get role ID by name
     */
    public static function getIdByName(string $name): ?int
    {
        return match(strtolower($name)) {
            self::ADMIN => self::ADMIN_ID,
            self::USER => self::USER_ID,
            self::SPV => self::SPV_ID,
            self::ADMINKREDIT => self::ADMINKREDIT_ID,
            self::MARKETING => self::MARKETING_ID,
            self::ANALIS => self::ANALIS_ID,
            self::RISK => self::RISK_ID,
            self::MITRA => self::MITRA_ID,
            default => null,
        };
    }
}
