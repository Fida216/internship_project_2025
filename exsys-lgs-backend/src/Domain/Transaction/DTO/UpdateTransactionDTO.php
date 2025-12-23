<?php

namespace App\Domain\Transaction\DTO;

use App\Shared\Enum\Currency;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateTransactionDTO
{
    #[Assert\NotBlank(message: 'Amount is required')]
    #[Assert\Regex(
        pattern: '/^\d+(\.\d{1,2})?$/',
        message: 'Amount must be a valid number with up to 2 decimal places'
    )]
    public string $amount;

    #[Assert\NotNull(message: 'Source currency is required')]
    public Currency $sourceCurrency;

    #[Assert\NotNull(message: 'Target currency is required')]
    public Currency $targetCurrency;

    #[Assert\NotBlank(message: 'Exchange rate is required')]
    #[Assert\Regex(
        pattern: '/^\d+(\.\d{1,6})?$/',
        message: 'Exchange rate must be a valid number with up to 6 decimal places'
    )]
    public string $exchangeRate;

    #[Assert\NotNull(message: 'Transaction date is required')]
    public \DateTime $transactionDate;

    
}
