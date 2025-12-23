<?php

namespace App\Domain\Transaction\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ExchangeOfficeIDQueryDTO
{
    #[Assert\NotBlank(message: 'Exchange Office ID is required')]
    #[Assert\Uuid(message: 'Exchange Office ID must be a valid UUID')]
    public string $exchangeOfficeId;
}
