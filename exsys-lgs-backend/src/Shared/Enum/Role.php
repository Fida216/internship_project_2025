<?php

namespace App\Shared\Enum;

enum Role: string
{
    case ADMIN = 'admin';
    case AGENT = 'agent';

    public function getLabel(): string
    {
        return match($this) {
            self::ADMIN => 'Administrator',
            self::AGENT => 'Agent',
        };
    }

    public function getSymfonyRole(): string
    {
        return match($this) {
            self::ADMIN => 'ROLE_ADMIN',
            self::AGENT => 'ROLE_AGENT',
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
}
