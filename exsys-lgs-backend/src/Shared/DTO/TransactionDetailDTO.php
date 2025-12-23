<?php

namespace App\Shared\DTO;

class TransactionDetailDTO
{
    public function __construct(
        public string $transactionId,
        public string $clientId,
        public string $bureauId,
        public string $currencyFrom,
        public string $currencyTo,
        public string $amount,
        public string $date,
        public string $firstName,
        public string $lastName,
        public string $clientPhone,
        public string $clientEmail,
        public string $clientAddress,
        public string $bureauName,
        public string $bureauAddress,
        public string $bureauPhone
    ) {}

    public function toArray(): array
    {
        return [
            'TransactionID' => $this->transactionId,
            'ClientID' => $this->clientId,
            'BureauID' => $this->bureauId,
            'CurrencyFrom' => $this->currencyFrom,
            'CurrencyTo' => $this->currencyTo,
            'Amount' => $this->amount,
            'Date' => $this->date,
            'FirstName' => $this->firstName,
            'LastName' => $this->lastName,
            'ClientPhone' => $this->clientPhone,
            'ClientEmail' => $this->clientEmail,
            'ClientAddress' => $this->clientAddress,
            'BureauName' => $this->bureauName,
            'BureauAddress' => $this->bureauAddress,
            'BureauPhone' => $this->bureauPhone
        ];
    }
}
