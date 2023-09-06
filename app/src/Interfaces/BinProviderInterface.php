<?php

namespace src\Interfaces;

interface BinProviderInterface
{
    public function isEuropean(int $bin): ?bool;
}