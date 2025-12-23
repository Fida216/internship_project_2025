<?php

namespace App\Shared\Enum;

enum Currency: string
{
    case USD = 'USD';
    case EUR = 'EUR';
    case GBP = 'GBP';
    case JPY = 'JPY';
    case CHF = 'CHF';
    case CAD = 'CAD';
    case AUD = 'AUD';
    case CNY = 'CNY';
    case MAD = 'MAD';
    case DZD = 'DZD';
    case TND = 'TND';
    case EGP = 'EGP';
    case SAR = 'SAR';
    case AED = 'AED';
    case QAR = 'QAR';
    case KWD = 'KWD';

    public function getLabel(): string
    {
        return match($this) {
            self::USD => 'US Dollar',
            self::EUR => 'Euro',
            self::GBP => 'British Pound',
            self::JPY => 'Japanese Yen',
            self::CHF => 'Swiss Franc',
            self::CAD => 'Canadian Dollar',
            self::AUD => 'Australian Dollar',
            self::CNY => 'Chinese Yuan',
            self::MAD => 'Moroccan Dirham',
            self::DZD => 'Algerian Dinar',
            self::TND => 'Tunisian Dinar',
            self::EGP => 'Egyptian Pound',
            self::SAR => 'Saudi Riyal',
            self::AED => 'UAE Dirham',
            self::QAR => 'Qatari Riyal',
            self::KWD => 'Kuwaiti Dinar',
        };
    }

    public function getSymbol(): string
    {
        return match($this) {
            self::USD => '$',
            self::EUR => '€',
            self::GBP => '£',
            self::JPY => '¥',
            self::CHF => 'CHF',
            self::CAD => 'C$',
            self::AUD => 'A$',
            self::CNY => '¥',
            self::MAD => 'DH',
            self::DZD => 'DA',
            self::TND => 'DT',
            self::EGP => 'E£',
            self::SAR => 'SR',
            self::AED => 'AED',
            self::QAR => 'QR',
            self::KWD => 'KD',
        };
    }

    public static function getAllValues(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    public static function getAllLabels(): array
    {
        $labels = [];
        foreach (self::cases() as $case) {
            $labels[$case->value] = $case->getLabel();
        }
        return $labels;
    }

    public static function getAllSymbols(): array
    {
        $symbols = [];
        foreach (self::cases() as $case) {
            $symbols[$case->value] = $case->getSymbol();
        }
        return $symbols;
    }
}
