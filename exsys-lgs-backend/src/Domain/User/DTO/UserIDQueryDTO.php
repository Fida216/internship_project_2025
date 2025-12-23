<?php

namespace App\Domain\User\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class UserIDQueryDTO
{
    #[Assert\NotBlank(message: 'User ID is required')]
    #[Assert\Uuid(message: 'User ID must be a valid UUID')]
    public string $userId;
}
