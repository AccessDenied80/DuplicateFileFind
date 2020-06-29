<?php
declare(strict_types=1);

namespace DuplicateFileFind;

class FileService
{
    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function fileArrayList() : array
    {
        $dir = new \DirectoryIterator($this->path);

        $fileArrayList = [];

        foreach($dir as $file) {
            if ($file->isFile() && $file->isReadable()) {
                $fileArrayList[$file->getPathname()] = $file->getSize();
            }
        }

        if (!empty($fileArrayList)) {
            asort($fileArrayList);
        }

        return $fileArrayList;
    }

    public function hash(string $file): string
    {
        return hash_file('sha256', $file);
    }

}