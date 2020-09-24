<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Loader;

class FilesystemLoader implements LoaderInterface
{
    private string $directory;

    private string $extension;

    public function __construct(string $directory, string $extension = '.blade.php')
    {
        $this->directory = $directory;
        $this->extension = $extension;
    }

    public function exists(string $name): bool
    {
        return isset($this->templates[$name]);
    }

    public function load(string $name): string
    {
        $path = $this->toPath($name);
        $templateString = @file_get_contents($path);
        if ($templateString === false) {
            throw new \RuntimeException('Failed to load template: '. $name);
        }
        return $templateString;
    }

    public function getTimestamp(string $name): int
    {
        $path = $this->toPath($name);
        $timestamp = filemtime($path);
        return $timestamp !== false ? $timestamp : -1;
    }

    private function toPath(string $name): string
    {
        return $this->directory . $name . $this->extension;
    }
}
