<?php

namespace src\ValueObjects;

use src\Interfaces\TransactionInterface;

class Transaction implements TransactionInterface
{
    public function __construct(private readonly int $bin, private readonly float $amount, private readonly string $currency)
    {

    }

    public function getBin(): int
    {
        return $this->bin;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }
}