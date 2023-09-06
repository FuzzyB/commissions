<?php

namespace src\ValueObjects;

class TransactionsFactory
{
    public function create(int $bin, float $amount, string $currency): Transaction
    {
        return new Transaction($bin, $amount, $currency);
    }
}