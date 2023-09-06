<?php
namespace src\Interfaces;

interface CurrencyExchangeRateProviderInterface
{
    public function getRate(string $currency): ?float;
}