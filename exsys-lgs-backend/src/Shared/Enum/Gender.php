<?php

namespace App\Shared\Enum;

enum Gender: string
{
    case MALE = 'male';
    case FEMALE = 'female';

    public function getLabel(): string
    {
        return match($this) {
            self::MALE => 'Male',
            self::FEMALE => 'Female',
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
