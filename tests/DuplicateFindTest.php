<?php
declare(strict_types=1);

namespace DuplicateFileFind\Tests;

use DuplicateFileFind\DuplicateFind;
use DuplicateFileFind\StoreResult;
use PHPUnit\Framework\TestCase;

final class DuplicateFindTest extends TestCase
{
    private string $dir;

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'dff_' . bin2hex(random_bytes(6));
        mkdir($this->dir);
    }

    protected function tearDown(): void
    {
        foreach (glob($this->dir . DIRECTORY_SEPARATOR . '*') as $file) {
            unlink($file);
        }
        rmdir($this->dir);
    }

    public function testReportsEveryFileOfEachDuplicateGroup(): void
    {
        $this->createFiles([
            'a1' => 'AAA', 'a2' => 'AAA', 'a3' => 'AAA',
            'b1' => 'BBB', 'b2' => 'BBB',
            'c1' => 'CCC',
        ]);

        $this->runFinder();

        $this->assertSame(
            $this->group(1, ['a1', 'a2', 'a3'], 'AAA')
            . PHP_EOL
            . $this->group(2, ['b1', 'b2'], 'BBB'),
            $this->readResult()
        );
    }

    public function testEmptyFilesAreGroupedLikeAnyOtherDuplicates(): void
    {
        $this->createFiles(['e1' => '', 'e2' => '', 'x' => 'X']);

        $this->runFinder();

        $this->assertSame($this->group(1, ['e1', 'e2'], ''), $this->readResult());
    }

    public function testSameSizeWithDifferentContentIsNotDuplicate(): void
    {
        $this->createFiles(['a' => 'AAA', 'b' => 'BBB']);

        $this->runFinder();

        $this->assertSame('No duplicates found' . PHP_EOL, $this->readResult());
    }

    public function testStaleResultIsOverwrittenWhenNoDuplicatesFound(): void
    {
        $this->createFiles(['x' => '1', 'y' => '22', StoreResult::FILE_NAME => 'stale']);

        $this->runFinder();

        $this->assertSame('No duplicates found' . PHP_EOL, $this->readResult());
    }

    public function testResultFileIsNotScannedAsInput(): void
    {
        $expected = 'No duplicates found' . PHP_EOL;
        // Розмір збігається з result.txt, який з'явиться після першого запуску.
        $this->createFiles(['twin' => $expected, 'other' => 'X']);

        $this->runFinder();
        $this->runFinder();

        $this->assertSame($expected, $this->readResult());
    }

    public function testThrowsWhenDirectoryHasNoFiles(): void
    {
        $this->expectExceptionMessage('No files in directory');

        $this->runFinder();
    }

    private function runFinder(): void
    {
        (new DuplicateFind($this->dir))->run();
    }

    /** @param array<string, string> $files */
    private function createFiles(array $files): void
    {
        foreach ($files as $name => $content) {
            file_put_contents($this->dir . DIRECTORY_SEPARATOR . $name, $content);
        }
    }

    /** @param list<string> $names */
    private function group(int $number, array $names, string $content): string
    {
        $lines = [sprintf(
            'Group %d: %d files, %d bytes, sha256 %s',
            $number,
            count($names),
            strlen($content),
            hash('sha256', $content)
        )];

        foreach ($names as $name) {
            $lines[] = $this->dir . DIRECTORY_SEPARATOR . $name;
        }

        return implode(PHP_EOL, $lines) . PHP_EOL;
    }

    private function readResult(): string
    {
        return (string) file_get_contents($this->dir . DIRECTORY_SEPARATOR . StoreResult::FILE_NAME);
    }
}
