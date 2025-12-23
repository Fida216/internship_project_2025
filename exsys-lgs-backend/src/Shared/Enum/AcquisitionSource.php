<?php

namespace App\Shared\Enum;

enum AcquisitionSource: string
{
    case ONLINE = 'online';
    case WALK_IN = 'walk_in';
    case REFERRAL = 'referral';
    case PHONE_CALL = 'phone_call';
    case EMAIL = 'email';
    case SOCIAL_MEDIA = 'social_media';
    case ADVERTISING = 'advertising';
    case PARTNERSHIP = 'partnership';
    case AGENT_DIRECT = 'agent_direct';
    case OTHER = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::ONLINE => 'Online',
            self::WALK_IN => 'Walk-in',
            self::REFERRAL => 'Referral',
            self::PHONE_CALL => 'Phone Call',
            self::EMAIL => 'Email',
            self::SOCIAL_MEDIA => 'Social Media',
            self::ADVERTISING => 'Advertising',
            self::PARTNERSHIP => 'Partnership',
            self::AGENT_DIRECT => 'Direct Agent',
            self::OTHER => 'Other',
        };
    }

    public static function getAllValues(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    public static function getAllLabels(): array
    {
        return array_map(fn($case) => $case->getLabel(), self::cases());
    }
}
