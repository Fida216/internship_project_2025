<?php

namespace App\Domain\ExchangeOffice\DTO;

use App\Shared\Enum\Status;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateExchangeOfficeDTO
{
    #[Assert\Length(min: 2, max: 150, minMessage: 'Name must be at least 2 characters', maxMessage: 'Name cannot exceed 150 characters')]
    public ?string $name = null;

    #[Assert\Length(min: 10, max: 255, minMessage: 'Address must be at least 10 characters')]
    public ?string $address = null;

    #[Assert\Email(message: 'Invalid email format')]
    public ?string $email = null;

    #[Assert\Regex(pattern: '/^\+?[0-9\s\-\(\)]{8,20}$/', message: 'Invalid phone number format')]
    public ?string $phone = null;

    #[Assert\Length(min: 2, max: 100, minMessage: 'Owner name must be at least 2 characters')]
    public ?string $owner = null;

    public ?Status $status = null;


    /**
     * Checks if at least one field has been provided for update
     */
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
