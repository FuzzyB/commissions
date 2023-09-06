<?php

namespace src;

require  __DIR__ . "/../../../autoload.php";

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use src\Interfaces\BinProviderInterface;
use src\Interfaces\CurrencyExchangeRateProviderInterface;
use src\Interfaces\TransactionInterface;
use src\Interfaces\TransactionsDataProviderInterface;
use src\ValueObjects\Transaction;

class CommissionsCalculatorTest extends TestCase
{
    private BinProviderInterface $binProvider;
    private CurrencyExchangeRateProviderInterface $currencyexchangeRateProvider;
    private TransactionsDataProviderInterface $transactionsDataProvider;




    /**
     * @param $bin
     * @param $amount
     * @param $currency
     * @param $rate
     * @param $isEuropean
     * @return CommissionsCalculator
     * @throws \Exception
     */
    public function getCalculator($bin, $amount, $currency, $rate, $isEuropean): CommissionsCalculator
    {
        $transaction1 = new Transaction($bin, $amount, $currency);
        $transactions = [
            $transaction1,
        ];
        $this->transactionsDataProvider->expects($this->once())->method('getRows')->with()->willReturn($transactions);

        $this->currencyexchangeRateProvider->expects($this->any())->method('getRate')->with($transaction1->getCurrency())
            ->willReturn($rate);

        $this->binProvider->expects($this->any())->method('isEuropean')->with($transaction1->getBin())
            ->willReturn($isEuropean);

        $calculator = new CommissionsCalculator($this->binProvider, $this->currencyexchangeRateProvider);
        $calculator->setTransactionsData($this->transactionsDataProvider);
        $calculator->calculate();
        return $calculator;
    }

    protected function setUp(): void
    {



        $this->binProvider = $this->createMock(BinProviderInterface::class);
        $this->currencyexchangeRateProvider = $this->createMock(CurrencyExchangeRateProviderInterface::class);
        $this->transactionsDataProvider = $this->createMock(TransactionsDataProviderInterface::class);
    }

    public function inputDataProvider()
    {
        return [
            [45717360, 100.00, 'EUR', 1.0, 1, true], //bin: denmark
            [516793, 50.00, 'USD', 1.07, 0.47, true], //bin: lithuania
            [45417360, 10000.00, 'JPY', 158.38, 0.64, true], //bin: denmark
            [41417360, 130.00, 'USD', 1.07, 2.43, false], //bin: USA
            [4745030, 2000.00, 'GBP', 0.85, 47.06, false], //bin: UK
        ];
    }

    /** @test
     * @dataProvider inputDataProvider
     */
    public function inputFileIsInCorrectFormat($bin, $amount, $currency, $rate, $expectedResult, $isEuropean)
    {
        $calculator = $this->getCalculator($bin, $amount, $currency, $rate, $isEuropean);
        $output = $calculator->getCommissionsAsString();

        $this->assertEquals(number_format($expectedResult, 2). "\n", $output);
    }

    /** @test */
    public function currencyExchangeRateProviderResultIsBroken()
    {
        $this->expectException(\Exception::class);
        $transaction1 = new Transaction(45717360, 100.00, 'EUR');
        $transactions = [
            $transaction1,
        ];

        $this->transactionsDataProvider->expects($this->once())->method('getRows')->with()->willReturn($transactions);

        $this->currencyexchangeRateProvider->expects($this->any())->method('getRate')->with($transaction1->getCurrency())
            ->willReturn(null);

        $this->binProvider->expects($this->any())->method('isEuropean')->with($transaction1->getBin())
            ->willReturn(true);

        $calculator = new CommissionsCalculator($this->binProvider, $this->currencyexchangeRateProvider);
        $calculator->setTransactionsData($this->transactionsDataProvider);
        $calculator->calculate();
    }

    /** @test */
    public function binProviderResultIsBroken()
    {
        $this->expectException(\Exception::class);
        $transaction1 = new Transaction(45717360, 100.00, 'EUR');
        $transactions = [
            $transaction1,
        ];

        $this->transactionsDataProvider->expects($this->once())->method('getRows')->with()->willReturn($transactions);

        $this->currencyexchangeRateProvider->expects($this->any())->method('getRate')->with($transaction1->getCurrency())
            ->willReturn(1.0);

        $this->binProvider->expects($this->any())->method('isEuropean')->with($transaction1->getBin())
            ->willReturn(null);

        $calculator = new CommissionsCalculator($this->binProvider, $this->currencyexchangeRateProvider);
        $calculator->setTransactionsData($this->transactionsDataProvider);
        $calculator->calculate();
    }


    /** @test
     * @dataProvider inputDataProvider
     */
    public function outputIsInCorrectFormat($bin, $amount, $currency, $rate, $expectedResult, $isEuropean)
    {
        $calculator = $this->getCalculator($bin, $amount, $currency, $rate, $isEuropean);
        $output = $calculator->getCommisions();

        $this->assertEquals($output[0], number_format($expectedResult, 2));
    }

    /** @test */
    public function noTransactionsProvided()
    {
        $transactions = [];
        $this->transactionsDataProvider->expects($this->once())->method('getRows')->with()->willReturn($transactions);

        $this->currencyexchangeRateProvider->expects($this->never())->method('getRate');

        $this->binProvider->expects($this->never())->method('isEuropean');

        $calculator = new CommissionsCalculator($this->binProvider, $this->currencyexchangeRateProvider);
        $calculator->setTransactionsData($this->transactionsDataProvider);
        $calculator->calculate();

        $output = $calculator->getCommissionsAsString();

        $this->assertEquals('', $output);
    }

}
