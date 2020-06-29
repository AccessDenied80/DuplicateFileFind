<?php
use DuplicateFileFind\DuplicateFind;

chdir(dirname(__DIR__));
require "vendor/autoload.php";

$pathToFind = realpath(__DIR__ . DIRECTORY_SEPARATOR . 'files');

try {
    (new DuplicateFind($pathToFind))->run();
} catch (\Throwable $e) {
    echo $e->getMessage() . PHP_EOL;
}