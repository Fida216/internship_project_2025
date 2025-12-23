<?php

namespace App\Domain\Client\DTO;

use App\Shared\Enum\AcquisitionSource;
use App\Shared\Enum\Gender;
use App\Shared\Enum\Status;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateClientDTO
{
    #[Assert\Length(min: 2, max: 100, minMessage: 'Last name must be at least 2 characters', maxMessage: 'Last name cannot exceed 100 characters')]
    public ?string $lastName = null;

    #[Assert\Length(min: 2, max: 100, minMessage: 'First name must be at least 2 characters', maxMessage: 'First name cannot exceed 100 characters')]
    public ?string $firstName = null;

    #[Assert\Date(message: 'Invalid date format')]
    public ?string $birthDate = null;

    #[Assert\Email(message: 'Invalid email format')]
    public ?string $email = null;

    #[Assert\Regex(pattern: '/^\+?[0-9\s\-\(\)]{8,20}$/', message: 'Invalid phone number format')]
    public ?string $phone = null;

    #[Assert\Regex(pattern: '/^\+?[0-9\s\-\(\)]{8,20}$/', message: 'Invalid WhatsApp number format')]
    public ?string $whatsapp = null;

    public ?string $nationalId = null;

    public ?string $passport = null;

    public ?string $nationality = null;

    public ?string $residence = null;

    public ?Gender $gender = null;

    public ?AcquisitionSource $acquisitionSource = null;

    public ?Status $status = null;

    public ?string $currentSegment = null;



    public function hasAnyField(): bool
    {
        $properties = get_object_vars($this);
        foreach ($properties as $value) {
            if ($value !== null) {
                return true;
            }
        }
        return false;
    }
}
