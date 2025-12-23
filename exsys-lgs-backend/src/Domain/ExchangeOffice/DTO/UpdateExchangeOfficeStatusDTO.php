<?php
namespace App\Domain\ExchangeOffice\DTO;

use App\Shared\Enum\Status;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateExchangeOfficeStatusDTO
{
    #[Assert\NotBlank(message: 'Status is required')]
    #[Assert\Choice(
        choices: ['active', 'inactive'],
        message: 'Status must be either "active" or "inactive"'
    )]
    public string $status;

    /**
     * Convertit le statut string en enum Status
     */
    public function getStatusEnum(): Status
    {
        try {
            return Status::from($this->status);
        } catch (\ValueError) {
            throw new \InvalidArgumentException("Invalid status value: {$this->status}. Allowed values: " . implode(', ', Status::getAllValues()));
        }
    }
}
