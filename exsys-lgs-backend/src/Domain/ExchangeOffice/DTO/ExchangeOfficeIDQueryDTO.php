<?php

namespace App\Domain\ExchangeOffice\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ExchangeOfficeIDQueryDTO
{
    #[Assert\NotBlank(message: 'ID is required')]
    #[Assert\Uuid(message: 'ID must be a valid UUID')]
    public string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
