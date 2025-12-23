<?php

namespace App\Domain\MarketingAction\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class MarketingActionQueryDTO
{
    #[Assert\NotBlank(message: 'Marketing action ID is required')]
    #[Assert\Uuid(message: 'Marketing action ID must be a valid UUID')]
    public ?string $id = null;

    public function setId(?string $id): void
    {
        $this->id = $id;
    }
    // Ajoutez d'autres propriétés de filtre si besoin
}
