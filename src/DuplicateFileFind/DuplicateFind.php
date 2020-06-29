<?php
declare(strict_types=1);

namespace DuplicateFileFind;

class DuplicateFind
{
    private FileService $fileService;
    private string $pathToFind;
    private array $fileArrayList = [];
    private array $result = [];

    public function __construct(string $pathToFind)
    {
        $this->fileService = new FileService($pathToFind);
        $this->pathToFind = $pathToFind;
    }

    public function run()
    {
        $this->getFileArrayList();
        $this->process();
        $this->storeResult();
    }

    private function getFileArrayList()
    {
        $this->fileArrayList = $this->fileService->fileArrayList();

        if (empty($this->fileArrayList)) {
            throw new \Exception('No files in directory');
        }

    }

    private function process() : void
    {
        $duplicatesTable = [];

        foreach ($this->fileArrayList as $file => $size) {
            $duplicatesTable[$size][] = $file;
        }

        $duplicatesTableHash = [];

        foreach ($duplicatesTable as $k => $f) {

            if (count($f) == 1) continue;

            foreach ($f as $fileName) {

                if ($k == 0) {
                    $duplicatesTableHash[$fileName] = 0;
                } else {
                    $duplicatesTableHash[$fileName] = $this->fileService->hash($fileName);
                }
            }

        }

        asort($duplicatesTableHash);
        $this->result = array_diff_assoc($duplicatesTableHash, array_unique($duplicatesTableHash));
    }

    private function storeResult() : void
    {
        if (!empty($this->result)) {
            $data = implode("\r\n", array_keys($this->result));
            (new StoreResult($this->pathToFind))->save($data);
        }
    }
}