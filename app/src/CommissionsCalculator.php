<?php
namespace src;

use src\Interfaces\BinProviderInterface;
use src\Interfaces\CurrencyExchangeRateProviderInterface;
use src\Interfaces\TransactionInterface;
use src\Interfaces\TransactionsDataProviderInterface;

class CommissionsCalculator {

    public const NOT_EURO_RATE = 0.02;
    public const EURO_RATE = 0.01;
    private TransactionsDataProviderInterface $transactionsData;

    private array $calcResults = [];

    public function __construct(
        private BinProviderInterface $binProvider,
        private CurrencyExchangeRateProviderInterface $currencyRateProvider
    ) {
    }

    public function setTransactionsData(
        TransactionsDataProviderInterface $transactionsDataProvider
    ): void
    {
        $this->transactionsData = $transactionsDataProvider;
    }

    public function calculate()
    {
        $transactions = $this->transactionsData->getRows();

        /** @var TransactionInterface $transaction */
        foreach ($transactions as $transaction) {

            $currencyRate = $this->currencyRateProvider->getRate($transaction->getCurrency());

            if (is_null($currencyRate)) {
                throw new \Exception('Unable retrieve currency exchange rate, service is down');
            }

            $isEuropean = $this->binProvider->isEuropean($transaction->getBin());
            if (is_null($isEuropean)) {
                throw new \Exception('Unable to predict BIN area, service is down');
            }

            $commission = $this->calc(
                $transaction->getAmount(),
                $transaction->getCurrency(),
                $isEuropean,
                $currencyRate
            );

            $this->storeCalcResult($commission);
        }
    }

    public function getCommissionsAsString(): string
    {
        $calcResult = $this->getCalcResult();
        $str = '';
        foreach ($calcResult as $commision) {
            $str .= $commision . "\n";
        }
        return $str;
    }

    private function calc(float $amount, $currency, bool $isEuropean, float $currencyRate): float
    {
        if($currencyRate !== 0.0 || $currency !== 'EUR') {
            $amount = $amount / $currencyRate;
        }

        $rate = ($isEuropean) ?  self::EURO_RATE : self::NOT_EURO_RATE;

        return ceil($amount * $rate*100.0)/100.0;
    }

    private function getCalcResult(): array
    {
        return $this->calcResults;
    }

    private function storeCalcResult(float $commission): void
    {
        $this->calcResults[] = number_format($commission, 2);
    }

    public function getCommisions()
    {
        return $this->getCalcResult();
    }
}