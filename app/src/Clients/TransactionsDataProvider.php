<?php

namespace src\Clients;

use src\Interfaces\TransactionsDataProviderInterface;
use src\ValueObjects\TransactionsFactory;

class TransactionsDataProvider implements TransactionsDataProviderInterface
{

    const SEPARATOR =",";

    public function __construct(
        private readonly FileGetContentsWrapper $fileGetContentsWrapper,
        private readonly TransactionsFactory $transactionFactory,
        private string $baseUrl
    )
    {
    }

    public function getRows(): array
    {
        $content = $this->fileGetContentsWrapper->fileGetContents($this->baseUrl);
        $rows = explode("\n", $content);

        $transactions = [];
        foreach ($rows as $row) {
            $item = json_decode($row);
            $transactions[] = $this->transactionFactory->create((int)$item->bin, (float)$item->amount, (string)$item->currency);
        }

        return $transactions;
    }
}