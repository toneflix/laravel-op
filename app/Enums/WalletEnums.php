<?php

namespace App\Enums;

enum WalletEnums: string
{
    case WALLET_MAIN = 'wallet_main';
    case WALLET_ESCROW = 'wallet_escrow';

    /**
     * Check if a given value is a valid enum case.
     */
    public static function isValid(string $type): bool
    {
        foreach (self::cases() as $case) {
            if ($case->value === $type) {
                return true;
            }
        }

        return false;
    }
}
