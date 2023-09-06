<?php

namespace src\Clients;

require  __DIR__ . "/../../../../autoload.php";

use PHPUnit\Framework\TestCase;
use src\ValueObjects\Transaction;
use src\ValueObjects\TransactionsFactory;

class TransactionsDataProviderTest extends TestCase
{

    private $inputFile;

    private string $input = '{"bin":"45717360","amount":"100.00","currency":"EUR"}
{"bin":"516793","amount":"50.00","currency":"USD"}
{"bin":"45417360","amount":"10000.00","currency":"JPY"}
{"bin":"41417360","amount":"130.00","currency":"USD"}
{"bin":"4745030","amount":"2000.00","currency":"GBP"}';

    private FileGetContentsWrapper $fileGetContentsWrapper;
    private string $baseUrl;
    protected function setUp(): void
    {
        parent::setUp();

        $this->inputFile = tmpfile();
        fwrite($this->inputFile, $this->input);
        fseek($this->inputFile, 0);

        $this->baseUrl = 'https://some-funny-url';
        $this->fileGetContentsWrapper = $this->createMock(FileGetContentsWrapper::class);
        $transactionsFactory = new TransactionsFactory();
        $this->dataProvider = new TransactionsDataProvider($this->fileGetContentsWrapper, $transactionsFactory, $this->baseUrl);

    }

    /** @test */
    public function getRowsSuccess()
    {
        $this->fileGetContentsWrapper
            ->expects($this->once())
            ->method('fileGetContents')
            ->with($this->baseUrl)
            ->willReturn($this->input);


        /** @var Transaction[] $transactions */
        $transactions = $this->dataProvider->getRows();

        $this->assertIsArray($transactions);
        $this->assertCount(5, $transactions);
        $this->assertEquals('EUR', $transactions[0]->getCurrency());
        $this->assertEquals('100.00', $transactions[0]->getAmount());
        $this->assertEquals('45717360', $transactions[0]->getBin());
    }
}