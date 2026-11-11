<?php
declare(strict_types=1);

namespace DuplicateFileFind\Tests;

use DuplicateFileFind\FileService;
use PHPUnit\Framework\TestCase;

final class FileServiceTest extends TestCase
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

    public function testListsFilesWithSizesSkippingExcluded(): void
    {
        file_put_contents($this->dir . '/a', 'AA');
        file_put_contents($this->dir . '/b', 'B');
        file_put_contents($this->dir . '/skip', 'SKIP');

        $list = (new FileService($this->dir, [$this->dir . '/skip']))->fileArrayList();

        ksort($list);
        $this->assertSame([$this->dir . '/a' => 2, $this->dir . '/b' => 1], $list);
    }

    public function testHashThrowsForUnreadableFile(): void
    {
        $this->expectException(\RuntimeException::class);

        (new FileService($this->dir))->hash($this->dir . '/missing');
    }
}
