<?php

namespace App\Domain\User\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CreateUserDTO
{
    #[Assert\NotBlank(message: 'Last name is required')]
    #[Assert\Length(max: 100, maxMessage: 'Last name cannot be longer than 100 characters')]
    public string $lastName;

    #[Assert\NotBlank(message: 'First name is required')]
    #[Assert\Length(max: 100, maxMessage: 'First name cannot be longer than 100 characters')]
    public string $firstName;

    #[Assert\NotBlank(message: 'Phone is required')]
    #[Assert\Regex(
        pattern: '/^\+?[1-9]\d{1,14}$/',
        message: 'Please provide a valid phone number'
    )]
    public string $phone;

    #[Assert\NotBlank(message: 'Role is required')]
    #[Assert\Choice(
        choices: ['admin', 'agent'],
        message: 'Role must be either admin or agent'
    )]
    public string $role;

    #[Assert\NotBlank(message: 'Email is required')]
    #[Assert\Email(message: 'Invalid email format')]
    public string $email;

    #[Assert\NotBlank(message: 'Password is required')]
    #[Assert\Length(
        min: 6,
        minMessage: 'Password must be at least 6 characters long'
    )]
    public string $password;

    #[Assert\Uuid(message: 'Exchange office ID must be a valid UUID')]
    public ?string $exchangeOfficeId = null;

    #[Assert\Choice(
        choices: ['active', 'inactive'],
        message: 'Status must be either active or inactive'
    )]
    public string $status = 'active';


}
