<?php
declare(strict_types=1);

namespace DuplicateFileFind;

class StoreResult
{
    public const FILE_NAME = 'result.txt';

    private string $file;

    public function __construct(string $pathToSave)
    {
        $this->file = self::filePath($pathToSave);
    }

    public static function filePath(string $pathToSave): string
    {
        return $pathToSave . DIRECTORY_SEPARATOR . self::FILE_NAME;
    }

    public function save(string $data): void
    {
        if (file_put_contents($this->file, $data) === false) {
            throw new \RuntimeException("Error save result file: {$this->file}");
        }
    }
}
