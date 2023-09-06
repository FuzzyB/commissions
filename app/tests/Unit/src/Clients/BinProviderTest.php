<?php

namespace src\Clients;

require  __DIR__ . "/../../../../autoload.php";
use PHPUnit\Framework\TestCase;


class BinProviderTest extends TestCase
{
    private FileGetContentsWrapper $fileGetContentsWrapper;
    private string $baseUrl;
    protected function setUp(): void
    {
        parent::setUp();
        $this->baseUrl = 'https://some-funny-url';
        $this->fileGetContentsWrapper = $this->createMock(FileGetContentsWrapper::class);
        $this->binProvider = new BinProvider($this->fileGetContentsWrapper, $this->baseUrl);

    }

    /** @test */
    public function isEuropean()
    {
        $bin = 45717360;
        $buildUrl = $this->baseUrl. '/' . $bin;
        $sampleJson = $this->getSampleEuJson();
        $this->fileGetContentsWrapper
            ->expects($this->any())
            ->method('fileGetContents')
            ->with($buildUrl)
            ->willReturn($sampleJson);

        $this->assertTrue($this->binProvider->isEuropean($bin));
    }

    /** @test */
    public function isNotEuropean()
    {
        $bin = 41417360;
        $sampleJson = $this->getSampleNotEuJson();
        $this->fileGetContentsWrapper
            ->expects($this->any())
            ->method('fileGetContents')
            ->willReturn($sampleJson);
        $this->assertFalse($this->binProvider->isEuropean($bin));
    }

    public function serviceIsDown()
    {
        $bin = 41417360;
        $this->assertEquals(null, $this->binProvider->isEuropean($bin));
    }

    private function getSampleEuJson(): string
    {
        $resultObject = new \stdClass();
        $resultObject->country = new \stdClass();
        $resultObject->country->alpha2 = 'AT';

        return json_encode($resultObject);
    }

    private function getSampleNotEuJson(): string
    {
        $resultObject = new \stdClass();
        $resultObject->country = new \stdClass();
        $resultObject->country->alpha2 = 'USA';

        return json_encode($resultObject);
    }
}
