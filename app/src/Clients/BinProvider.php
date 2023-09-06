<?php

namespace src\Clients;

use src\Interfaces\BinProviderInterface;

class BinProvider implements BinProviderInterface
{
    public function __construct(
        private readonly FileGetContentsWrapper $fileGetContentsWrapper,
        private string $baseUrl
    )
    {
    }

    public function isEuropean(int $bin): ?bool
    {
        $binResult = $this->fileGetContentsWrapper->fileGetContents($this->getUrl($bin));
        if (empty($binResult)) {
            return null;
        }

        $binResultObject = json_decode($binResult);
        if (empty($binResultObject->country->alpha2)) {
            return null;
        }


        return $this->isEu($binResultObject->country->alpha2);

    }

    private function getUrl(int $bin): string
    {
        return $this->baseUrl . '/' . $bin;
    }

    private function isEu($alpha2)
    {
        $euCountries = [
            'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GR', 'HR', 'HU',
            'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PO', 'PT', 'RO', 'SE', 'SI', 'SK',
        ];

        return in_array($alpha2, $euCountries);
    }
}