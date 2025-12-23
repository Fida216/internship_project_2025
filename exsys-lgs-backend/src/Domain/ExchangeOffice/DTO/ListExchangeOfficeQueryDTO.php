<?php
namespace App\Domain\ExchangeOffice\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ListExchangeOfficeQueryDTO
{
    #[Assert\Choice(choices: ['active', 'inactive'], message: 'Status must be "active" or "inactive"')]
    public ?string $status = null;

    #[Assert\Uuid(message: 'ID must be a valid UUID')]
    public ?string $id = null;
}
