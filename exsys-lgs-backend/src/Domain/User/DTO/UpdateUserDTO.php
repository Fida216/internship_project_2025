<?php

namespace App\Domain\User\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateUserDTO
{
    #[Assert\Length(max: 100, maxMessage: 'Last name cannot be longer than 100 characters')]
    public ?string $lastName = null;

    #[Assert\Length(max: 100, maxMessage: 'First name cannot be longer than 100 characters')]
    public ?string $firstName = null;

    #[Assert\Regex(
        pattern: '/^\+?[1-9]\d{1,14}$/',
        message: 'Please provide a valid phone number'
    )]
    public ?string $phone = null;

    #[Assert\Email(message: 'Please provide a valid email address')]
    public ?string $email = null;

    #[Assert\Choice(
        choices: ['active', 'inactive'],
        message: 'Status must be either active or inactive'
    )]
    public ?string $status = null;

}
