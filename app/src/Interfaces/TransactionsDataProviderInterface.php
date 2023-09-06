<?php
namespace src\Interfaces;

interface TransactionsDataProviderInterface
{
    public function getRows(): array;
}