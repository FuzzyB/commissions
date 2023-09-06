<?php

namespace src\Clients;

use src\Interfaces\CurrencyExchangeRateProviderInterface;

class CurrencyExchangeRateProvider implements CurrencyExchangeRateProviderInterface
{

    private ?array $rates;

    public function __construct(
        private readonly FileGetContentsWrapper $fileGetContentsWrapper,
        private string $baseUrl,
        private string $baseCurrency
    )
    {
    }

    public function getRate(string $currency): ?float
    {
        if (strtolower($currency) === strtolower($this->baseCurrency)) {
            return 1;
        }

        $rates = $this->getRates();
        return $rates[$currency];
    }

    private function getLatestUrl()
    {
        return $this->baseUrl . '?base=' . $this->baseCurrency;
    }

    private function getRates()
    {
        if (empty($this->rates)) {
            $this->rates = $this->getLatestFromApi();
        }

        return $this->rates;
    }

    private function getLatestFromApi()
    {
        $result = $this->fileGetContentsWrapper->fileGetContents($this->getLatestUrl());

        if (empty($result)) {
            return null;
        }
        $rates = json_decode($result, true);
        if (empty($rates['rates'])) {
            return null;
        }

        return $rates['rates'];
    }
}