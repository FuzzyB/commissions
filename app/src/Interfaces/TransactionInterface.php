<?php

namespace src\Interfaces;

interface TransactionInterface
{
    public function getBin(): int;

    public function getAmount(): float;

    public function getCurrency(): string;
}