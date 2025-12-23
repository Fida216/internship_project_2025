<?php

namespace App\Domain\ExchangeOffice\DTO;

class ExchangeOfficeResponseDTO
{
    public string $id;
    public string $name;
    public string $address;
    public string $email;
    public string $phone;
    public string $owner;
    public string $status;
    public ?string $createdAt;

    public function __construct(array $data = [])
    {
        foreach ($data as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }
    }

    /**
     * Converts the DTO to an array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'email' => $this->email,
            'phone' => $this->phone,
            'owner' => $this->owner,
            'status' => $this->status,
            'createdAt' => $this->createdAt
        ];
    }
}
