<?php

namespace App\Domain\User\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class UserFilterDTO
{
    #[Assert\Uuid(message: 'Exchange office ID must be a valid UUID')]
    public ?string $exchangeOfficeId = null;

    #[Assert\Choice(
        choices: ['admin', 'agent'],
        message: 'Role must be either admin or agent'
    )]
    public ?string $role = null;

    #[Assert\Choice(
        choices: ['active', 'inactive'],
        message: 'Status must be either active or inactive'
    )]
    public ?string $status = null;


    
}
