<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Benchmarks;

trait TemporaryDirectoryTrait
{
    private $temporaryDirectories = [];

    private function createTemporaryDirectory(int $mode = 0700): string
    {
        do {
            $directory = sys_get_temp_dir() . '/' . uniqid('phpbench_');
        } while (@mkdir($directory, $mode) === false);
        $this->temporaryDirectories[] = $directory;
        return $directory;
    }

    private function disposeTemporaryDirectories(): void
    {
        foreach ($this->temporaryDirectories as $directory) {
            foreach (new \DirectoryIterator($directory) as $file) {
                if ($file->isFile()) {
                    unlink($file->getPathname());
                }
            }
            rmdir($directory);
        }
    }
}
