<?php

namespace App\Domain\ExchangeOffice\DTO;

class ListExchangeOfficesResponseDTO
{
    /** @var ExchangeOfficeResponseDTO[] */
    public array $exchangeOffices;
    public int $total;
    public array $filters;

    public function __construct(array $exchangeOffices, array $filters = [])
    {
        $this->exchangeOffices = $exchangeOffices;
        $this->total = count($exchangeOffices);
        $this->filters = $filters;
    }

    public function toArray(): array
    {
        return [
            'exchangeOffices' => array_map(fn($office) => $office->toArray(), $this->exchangeOffices),
            'total' => $this->total,
            'filters' => $this->filters
        ];
    }
}
