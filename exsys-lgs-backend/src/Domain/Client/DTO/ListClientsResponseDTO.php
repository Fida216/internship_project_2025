<?php

namespace App\Domain\Client\DTO;

class ListClientsResponseDTO
{
    /** @var ClientResponseDTO[] */
    public array $clients;
    public int $total;
    public ?int $totalClients = null;
    public ?int $totalPages = null;
    public ?int $currentPage = null;
    public ?bool $hasNextPage = null;
    public ?bool $hasPreviousPage = null;

    public function __construct(array $clients)
    {
        $this->clients = $clients;
        $this->total = count($clients);
    }

    public function toArray(): array
    {
        $result = [
            'clients' => array_map(fn($client) => $client->toArray(), $this->clients),
            'total' => $this->total
        ];


        if ($this->totalClients !== null) {
            $result['totalClients'] = $this->totalClients;
        }
        if ($this->totalPages !== null) {
            $result['totalPages'] = $this->totalPages;
        }
        if ($this->currentPage !== null) {
            $result['currentPage'] = $this->currentPage;
        }
        if ($this->hasNextPage !== null) {
            $result['hasNextPage'] = $this->hasNextPage;
        }
        if ($this->hasPreviousPage !== null) {
            $result['hasPreviousPage'] = $this->hasPreviousPage;
        }

        return $result;
    }
}
