<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Cache;

use Emonkak\Sharp\FileTemplate;
use Emonkak\Sharp\TemplateInterface;

class FilesystemCache implements CacheInterface
{
    private string $cacheDirectory;

    public function __construct(string $cacheDirectory)
    {
        $this->cacheDirectory = $cacheDirectory;
    }

    public function load(string $key): ?TemplateInterface
    {
        $path = $this->toPath($key);
        return new FileTemplate($path);
    }

    public function write(string $key, TemplateInterface $template): void
    {
        $path = $this->toPath($key);
        $directory = dirname($path);

        if (!is_dir($directory)) {
            if (!@mkdir($directory, 0700, true)) {
                throw new \RuntimeException('Unable to create the directory: ' . $directory);
            }
        }

        $source = $template->getSource();
        if (@file_put_contents($path, $source) === false) {
            throw new \RuntimeException('Unable to write the source: ' . $path);
        }
    }

    public function exists(string $key): bool
    {
        $path = $this->toPath($key);
        return file_exists($path);
    }

    public function getTimestamp(string $key): int
    {
        $path = $this->toPath($key);
        $timestamp = @filemtime($path);
        return $timestamp !== false ? $timestamp : -1;
    }

    private function toPath(string $key): string
    {
        return $this->cacheDirectory . $key . '.php';
    }
}
