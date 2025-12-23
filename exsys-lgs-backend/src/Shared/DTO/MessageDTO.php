<?php

namespace App\Shared\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class MessageDTO
{
    #[Assert\NotBlank(message: 'The message is required')]
    public string $message;

    #[Assert\NotBlank(message: 'The language is required')]
    #[Assert\Choice(
        choices: ['french', 'english', 'arabic'],
        message: 'The language must be one of the following: {{ choices }}'
    )]
    public string $language = 'french';
}
