<?php

namespace App\Domain\User\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ChangePasswordDTO
{
    #[Assert\NotBlank(message: 'Current password is required')]
    public string $oldPassword;

    #[Assert\NotBlank(message: 'New password is required')]
    #[Assert\Length(
        min: 6,
        minMessage: 'New password must be at least 6 characters long'
    )]
    public string $newPassword;

}
