<?php
namespace App\Shared\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class StandardFilterDTO
{

    #[Assert\Positive(message: 'Page must be a positive number')]
    public int $page = 1;

    #[Assert\Positive(message: 'Limit must be a positive number')]
    public int $limit = 20;

    
}
