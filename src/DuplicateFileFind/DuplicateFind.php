<?php
declare(strict_types=1);

namespace DuplicateFileFind;

class DuplicateFind
{
    private FileService $fileService;
    private string $pathToFind;

    /** @var array<string, int> Path => size in bytes */
    private array $fileArrayList = [];

    /** @var list<array{hash: string, size: int, files: list<string>}> */
    private array $result = [];

    public function __construct(string $pathToFind)
    {
        $this->pathToFind = $pathToFind;
        // Файл результату лежить у тій самій папці — його не скануємо.
        $this->fileService = new FileService($pathToFind, [StoreResult::filePath($pathToFind)]);
    }

    public function run(): void
    {
        $this->getFileArrayList();
        $this->process();
        $this->storeResult();
    }

    private function getFileArrayList(): void
    {
        $this->fileArrayList = $this->fileService->fileArrayList();

        if (empty($this->fileArrayList)) {
            throw new \Exception('No files in directory');
        }
    }

    private function process(): void
    {
        $filesBySize = [];

        foreach ($this->fileArrayList as $file => $size) {
            $filesBySize[$size][] = $file;
        }

        $groups = [];

        foreach ($filesBySize as $size => $files) {
            // Файли унікального розміру не можуть мати дублікатів — не хешуємо.
            if (count($files) < 2) {
                continue;
            }

            $filesByHash = [];

            foreach ($files as $file) {
                $filesByHash[$this->fileService->hash($file)][] = $file;
            }

            foreach ($filesByHash as $hash => $sameFiles) {
                if (count($sameFiles) < 2) {
                    continue;
                }

                sort($sameFiles);
                $groups[] = ['hash' => (string) $hash, 'size' => (int) $size, 'files' => $sameFiles];
            }
        }

        usort($groups, fn (array $a, array $b): int => strcmp($a['files'][0], $b['files'][0]));

        $this->result = $groups;
    }

    private function storeResult(): void
    {
        (new StoreResult($this->pathToFind))->save($this->formatResult());
    }

    private function formatResult(): string
    {
        if (empty($this->result)) {
            return 'No duplicates found' . PHP_EOL;
        }

        $blocks = [];

        foreach ($this->result as $index => $group) {
            $lines = [sprintf(
                'Group %d: %d files, %d bytes, sha256 %s',
                $index + 1,
                count($group['files']),
                $group['size'],
                $group['hash']
            )];

            foreach ($group['files'] as $file) {
                $lines[] = $file;
            }

            $blocks[] = implode(PHP_EOL, $lines) . PHP_EOL;
        }

        return implode(PHP_EOL, $blocks);
    }
}
