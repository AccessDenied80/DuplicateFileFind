<?php

namespace DuplicateFileFind;

class StoreResult
{
    private $path;

    public function __construct(string $pathToSave)
    {
        $this->path = $pathToSave;
    }

    public function save(string $data)
    {
        $file = $this->path . DIRECTORY_SEPARATOR . 'result.txt';
        $handle = fopen($file, "w");

        if ($handle === false)
            throw new \Exception("Error save result file: $file");

        fwrite($handle, $data);
        fclose($handle);
    }

}