<?php

namespace App\Domain\QuickMessage\DTO;

use App\Shared\Enum\ChannelType;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

class QuickMessageCreateDTO
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
    
    #[Assert\NotBlank(message: 'Targets are required')]
    #[Assert\Count(
        min: 1,
        minMessage: 'At least one target client is required'
    )]
    #[Assert\Valid]
    /**
     * @var QuickMessageTargetClientDTO[]
     */
    public array $targets = [];
}
