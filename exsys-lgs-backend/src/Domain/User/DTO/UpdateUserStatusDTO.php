<?php

namespace App\Domain\User\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateUserStatusDTO
{

    #[Assert\NotBlank(message: 'Status is required')]
    #[Assert\Choice(
        choices: ['active', 'inactive'],
        message: 'Status must be either active or inactive'
    )]
    public string $status;

}
