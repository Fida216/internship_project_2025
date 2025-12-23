<?php

namespace App\Domain\User\DTO;

class UsersListResponseDTO
{
    public array $users;
    public int $total;

    public function __construct(array $users, int $total)
    {
        $this->users = $users;
        $this->total = $total;
    }

    public function toArray(): array
    {
        return [
            'users' => $this->users,
            'total' => $this->total,
        ];
    }
}
