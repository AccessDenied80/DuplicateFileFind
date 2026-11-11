<?php
use DuplicateFileFind\DuplicateFind;

require __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$pathToFind = realpath(__DIR__ . DIRECTORY_SEPARATOR . 'files');

if ($pathToFind === false) {
    echo 'Directory "files" not found' . PHP_EOL;
    exit(1);
}

try {
    (new DuplicateFind($pathToFind))->run();
} catch (\Throwable $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(1);
}
