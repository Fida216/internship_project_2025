<?php

namespace App\Domain\MarketingAction\DTO;

use App\Shared\Enum\ChannelType;
use Symfony\Component\Validator\Constraints as Assert;

class MarketingActionCreateDTO
{
    #[Assert\NotBlank(message: 'Title is required')]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: 'Title must contain at least {{ limit }} characters',
        maxMessage: 'Title cannot exceed {{ limit }} characters'
    )]
    public string $title;

    #[Assert\NotBlank(message: 'Channel type is required')]
    #[Assert\Choice(
        choices: ['email', 'sms', 'whatsapp'],
        message: 'Channel type must be: email, sms or whatsapp'
    )]
    public string $channelType;

    #[Assert\NotBlank(message: 'Content is required')]
    public string $content;

    #[Assert\NotBlank(message: 'Campaign ID is required')]
    #[Assert\Uuid(message: 'Campaign ID must be a valid UUID')]
    public string $campaignId;
}
