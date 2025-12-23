<?php

namespace App\Domain\Client\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use App\Shared\DTO\StandardFilterDTO;

class ClientsFilterDTO extends StandardFilterDTO
{

    public ?string $search = null;

    #[Assert\Choice(choices: ['active', 'inactive'], message: 'Invalid status')]
    public ?string $status = null;

    #[Assert\Length(max: 100, maxMessage: 'Nationality too long')]
    public ?string $nationality = null;

    #[Assert\Choice(choices: ['male', 'female'], message: 'Gender must be male or female')]
    public ?string $gender = null;

    #[Assert\Choice(
        choices: ['online', 'walk_in', 'referral', 'phone_call', 'email', 'social_media', 'advertising', 'partnership', 'agent_direct', 'other'],
        message: 'Invalid acquisition source'
    )]
    public ?string $acquisitionSource = null;
    // Filter by current segment
    public ?string $currentSegment = null;
}
