<?php

namespace App\Domain\Client\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use App\Shared\DTO\StandardFilterDTO;

class AdminClientsFilterDTO extends StandardFilterDTO
{
    #[Assert\Uuid(message: 'Invalid UUID format for exchange office')]
    public ?string $exchangeOfficeId = null;

    #[Assert\Choice(choices: ['active', 'inactive'], message: 'Invalid status')]
    public ?string $status = null;

    public ?string $search = null;

    public ?string $nationality = null;

    #[Assert\Choice(choices: ['male', 'female'], message: 'Invalid gender')]
    public ?string $gender = null;

    #[Assert\Choice(
        choices: ['online', 'walk_in', 'referral', 'phone_call', 'email', 'social_media', 'advertising', 'partnership', 'agent_direct', 'other'],
        message: 'Invalid acquisition source'
    )]
    public ?string $acquisitionSource = null;
    // Filter by current segment
    public ?string $currentSegment = null;

}
