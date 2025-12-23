<?php

namespace App\Domain\Client\DTO;

use App\Shared\Enum\AcquisitionSource;
use App\Shared\Enum\Gender;
use App\Shared\Enum\Status;
use Symfony\Component\Validator\Constraints as Assert;

class CreateClientDTO
{
    #[Assert\NotBlank(message: 'Last name is required')]
    #[Assert\Length(min: 2, max: 100, minMessage: 'Last name must be at least 2 characters', maxMessage: 'Last name cannot exceed 100 characters')]
    public string $lastName;

    #[Assert\NotBlank(message: 'First name is required')]
    #[Assert\Length(min: 2, max: 100, minMessage: 'First name must be at least 2 characters', maxMessage: 'First name cannot exceed 100 characters')]
    public string $firstName;

    #[Assert\NotBlank(message: 'Birth date is required')]
    #[Assert\Date(message: 'Invalid date format')]
    public string $birthDate;

    #[Assert\NotBlank(message: 'Email is required')]
    #[Assert\Email(message: 'Invalid email format')]
    public string $email;

    #[Assert\NotBlank(message: 'Phone number is required')]
    #[Assert\Regex(pattern: '/^\+?[0-9\s\-\(\)]{8,20}$/', message: 'Invalid phone number format')]
    public string $phone;

    #[Assert\Regex(pattern: '/^\+?[0-9\s\-\(\)]{8,20}$/', message: 'Invalid WhatsApp number format')]
    public ?string $whatsapp = null;

    public ?string $nationalId = null;

    public ?string $passport = null;

    #[Assert\NotBlank(message: 'Nationality is required')]
    public string $nationality;

    #[Assert\NotBlank(message: 'Residence is required')]
    public string $residence;

    #[Assert\NotNull(message: 'Gender is required')]
    public Gender $gender;

    #[Assert\NotNull(message: 'Acquisition source is required')]
    public AcquisitionSource $acquisitionSource;

    public ?string $currentSegment = null;


    #[Assert\Callback]
    public function validateIdentityDocument(): void
    {
        if (empty($this->nationalId) && empty($this->passport)) {
            throw new \InvalidArgumentException('At least one of nationalId or passport is required');
        }
    }
}
