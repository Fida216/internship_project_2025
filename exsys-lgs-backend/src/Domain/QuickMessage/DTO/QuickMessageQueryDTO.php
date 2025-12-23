<?php

namespace App\Domain\QuickMessage\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class QuickMessageQueryDTO
{
    #[Assert\NotBlank(message: 'Quick message ID is required')]
    #[Assert\Uuid(message: 'Quick message ID must be a valid UUID')]
    public ?string $id = null;

    public function setId(?string $id): void
    {
        $this->id = $id;
    }
    // Ajoutez d'autres propriétés de filtre si besoin
}
