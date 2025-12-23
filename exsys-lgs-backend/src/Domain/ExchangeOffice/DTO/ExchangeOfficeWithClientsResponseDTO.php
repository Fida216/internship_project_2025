<?php

namespace App\Domain\ExchangeOffice\DTO;

use App\Domain\Client\DTO\ClientResponseDTO;

class ExchangeOfficeWithClientsResponseDTO
{
    public string $id;
    public string $name;
    public string $address;
    public string $email;
    public string $phone;
    public string $owner;
    public string $status;
    public string $createdAt;
    /** @var ClientResponseDTO[] */
    public array $clients;
    public int $totalClients;

    public function __construct(
        string $id,
        string $name,
        string $address,
        string $email,
        string $phone,
        string $owner,
        string $status,
        string $createdAt,
        array $clients = [],
        int $totalClients = 0
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->address = $address;
        $this->email = $email;
        $this->phone = $phone;
        $this->owner = $owner;
        $this->status = $status;
        $this->createdAt = $createdAt;
        $this->clients = $clients;
        $this->totalClients = $totalClients;
    }

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
            'createdAt' => $this->createdAt,
            'clients' => array_map(fn(ClientResponseDTO $client) => $client->toArray(), $this->clients),
            'totalClients' => $this->totalClients
        ];
    }
}
