<?php

namespace App\Domain\ExchangeOffice\DTO;

class AllExchangeOfficesWithClientsResponseDTO
{
    /** @var ExchangeOfficeWithClientsResponseDTO[] */
    public array $exchangeOffices;
    public int $totalExchangeOffices;
    public int $totalClientsAcrossAllOffices;

    public function __construct(array $exchangeOffices = [])
    {
        $this->exchangeOffices = $exchangeOffices;
        $this->totalExchangeOffices = count($exchangeOffices);
        $this->totalClientsAcrossAllOffices = array_sum(
            array_map(fn(ExchangeOfficeWithClientsResponseDTO $office) => $office->totalClients, $exchangeOffices)
        );
    }

    public function toArray(): array
    {
        return [
            'exchangeOffices' => array_map(fn(ExchangeOfficeWithClientsResponseDTO $office) => $office->toArray(), $this->exchangeOffices),
            'totalExchangeOffices' => $this->totalExchangeOffices,
            'totalClientsAcrossAllOffices' => $this->totalClientsAcrossAllOffices
        ];
    }
}
