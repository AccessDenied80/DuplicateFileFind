<?php
declare(strict_types=1);

namespace DuplicateFileFind;

class FileService
{
    private string $path;

    /** @var array<string, true> */
    private array $excludedFiles = [];

    /** @param list<string> $excludedFiles Paths that must not be scanned */
    public function __construct(string $path, array $excludedFiles = [])
    {
        $this->path = $path;

        foreach ($excludedFiles as $file) {
            $this->excludedFiles[$file] = true;
        }
    }

    /** @return array<string, int> Path => size in bytes */
    public function fileArrayList(): array
    {
        $dir = new \DirectoryIterator($this->path);

        $fileArrayList = [];

        foreach ($dir as $file) {
            if (!$file->isFile() || !$file->isReadable()) {
                continue;
            }

            if (isset($this->excludedFiles[$file->getPathname()])) {
                continue;
            }

            $fileArrayList[$file->getPathname()] = $file->getSize();
        }

        return $fileArrayList;
    }

    public function hash(string $file): string
    {
        $hash = is_file($file) && is_readable($file) ? hash_file('sha256', $file) : false;

        if ($hash === false) {
            throw new \RuntimeException("Cannot read file: $file");
        }

        return $hash;
    }
}
