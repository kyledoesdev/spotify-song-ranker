<?php

namespace App\Enums;

enum LegalDocumentType: string
{
    case TERMS = 'terms';
    case PRIVACY = 'privacy';

    public function label(): string
    {
        return match ($this) {
            self::TERMS => 'Terms of Service',
            self::PRIVACY => 'Privacy Policy',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::TERMS => 'fa-file-contract',
            self::PRIVACY => 'fa-shield-halved',
        };
    }

    public function filamentColor(): string
    {
        return match ($this) {
            self::TERMS => 'warning',
            self::PRIVACY => 'info',
        };
    }
}
