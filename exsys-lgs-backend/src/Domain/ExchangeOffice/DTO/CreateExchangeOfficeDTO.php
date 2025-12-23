<?php

namespace App\Domain\ExchangeOffice\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CreateExchangeOfficeDTO
{
    #[Assert\NotBlank(message: 'Exchange office name is required')]
    #[Assert\Length(min: 2, max: 150, minMessage: 'Name must be at least 2 characters', maxMessage: 'Name cannot exceed 150 characters')]
    public string $name;

    #[Assert\NotBlank(message: 'Address is required')]
    #[Assert\Length(min: 10, max: 255, minMessage: 'Address must be at least 10 characters')]
    public string $address;

    #[Assert\NotBlank(message: 'Email is required')]
    #[Assert\Email(message: 'Invalid email format')]
    public string $email;

    #[Assert\NotBlank(message: 'Phone number is required')]
    #[Assert\Regex(pattern: '/^\+?[0-9\s\-\(\)]{8,20}$/', message: 'Invalid phone number format')]
    public string $phone;

    #[Assert\NotBlank(message: 'Owner name is required')]
    #[Assert\Length(min: 2, max: 100, minMessage: 'Owner name must be at least 2 characters')]
    public string $owner;

    public function __construct(
        string $name,
        string $address,
        string $email,
        string $phone,
        string $owner
    ) {
        $this->name = $name;
        $this->address = $address;
        $this->email = $email;
        $this->phone = $phone;
        $this->owner = $owner;
    }
}
