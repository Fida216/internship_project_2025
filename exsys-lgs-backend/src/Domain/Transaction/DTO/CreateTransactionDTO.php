<?php

namespace App\Domain\Transaction\DTO;

use App\Shared\Enum\Currency;
use Symfony\Component\Validator\Constraints as Assert;

class CreateTransactionDTO
{
    #[Assert\NotBlank(message: "Client ID is required")]
    #[Assert\Uuid(message: "Client ID must be a valid UUID")]
    public  string $clientId;

    #[Assert\NotBlank(message: "Amount is required")]
    #[Assert\Regex(
        pattern: '/^\d+(\.\d{1,2})?$/', 
        message: "Amount must be a valid decimal number with up to 2 decimal places"
    )]
    #[Assert\Range(
        min: 0.01,
        max: 999999999.99,
        notInRangeMessage: "Amount must be between 0.01 and 999,999,999.99"
    )]
    public  string $amount;

    #[Assert\NotNull(message: "Source currency is required")]
    public Currency $sourceCurrency;

    #[Assert\NotNull(message: "Target currency is required")]
    public Currency $targetCurrency;

    #[Assert\NotBlank(message: "Exchange rate is required")]
    #[Assert\Regex(
        pattern: '/^\d+(\.\d{1,6})?$/', 
        message: "Exchange rate must be a valid decimal number with up to 6 decimal places"
    )]
    #[Assert\Range(
        min: 0.000001,
        max: 999999.999999,
        notInRangeMessage: "Exchange rate must be between 0.000001 and 999,999.999999"
    )]
    public string $exchangeRate;

    #[Assert\NotNull(message: "Transaction date is required")]
    #[Assert\Type(type: \DateTimeInterface::class, message: "Transaction date must be a valid date")]
    public \DateTime $transactionDate;


}
