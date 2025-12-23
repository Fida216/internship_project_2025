<?php

namespace App\Domain\User\DTO;

class UserResponseDTO
{
    public string $id;
    public string $lastName;
    public string $firstName;
    public string $phone;
    public string $role;
    public string $status;
    public string $email;
    public string $createdAt;
    public ?array $exchangeOffice;

    public function __construct(
        string $id,
        string $lastName,
        string $firstName,
        string $phone,
        string $role,
        string $status,
        string $email,
        string $createdAt,
        ?array $exchangeOffice = null
    ) {
        $this->id = $id;
        $this->lastName = $lastName;
        $this->firstName = $firstName;
        $this->phone = $phone;
        $this->role = $role;
        $this->status = $status;
        $this->email = $email;
        $this->createdAt = $createdAt;
        $this->exchangeOffice = $exchangeOffice;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'lastName' => $this->lastName,
            'firstName' => $this->firstName,
            'phone' => $this->phone,
            'role' => $this->role,
            'status' => $this->status,
            'email' => $this->email,
            'createdAt' => $this->createdAt,
            'exchangeOffice' => $this->exchangeOffice,
        ];
    }
}
