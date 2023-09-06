<?php

namespace src\Clients;

require  __DIR__ . "/../../../../autoload.php";
use PHPUnit\Framework\TestCase;

class CurrencyExchangeRateProviderTest extends TestCase
{
    private FileGetContentsWrapper $fileGetContentsWrapper;
    private string $baseUrl;

    protected function setUp(): void
    {
        parent::setUp();
        $this->baseUrl = 'https://some-funny-url/latest';
        $this->fileGetContentsWrapper = $this->createMock(FileGetContentsWrapper::class);
        $this->currencyExchangeRateProvider = new CurrencyExchangeRateProvider($this->fileGetContentsWrapper, $this->baseUrl, $baseCurrency = 'EUR');

    }

    /** @test */
    public function getRateSuccess()
    {
        $buildUrl = $this->baseUrl. '?base=EUR';
        $sampleJson = $this->getSampleSuccessJson();
        $this->fileGetContentsWrapper
            ->expects($this->once())
            ->method('fileGetContents')
            ->with($buildUrl)
            ->willReturn($sampleJson);

        $this->assertEquals(1, $this->currencyExchangeRateProvider->getRate('EUR'));
        $this->assertEquals(0.84, $this->currencyExchangeRateProvider->getRate('USD'));
    }


    private function getSampleSuccessJson()
    {
        $r = new \stdClass();
        $r->base = 'EUR';
        $r->rates = new \stdClass();
        $r->rates->USD = 0.84;
        $r->rates->GBP = 0.9;
        $r->rates->JPY = 0.9;

        return json_encode($r);
    }
}
