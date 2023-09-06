<?php

namespace src\Clients;

class FileGetContentsWrapper
{
    public function fileGetContents(string $filename): string
    {
        return file_get_contents($filename);
    }
}