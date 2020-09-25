<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Loader;

class FilesystemLoader implements LoaderInterface
{
    /**
     * @var string[]
     */
    private array $directories;

    private string $extension;

    /**
     * @param string[] $directories
     */
    public function __construct(array $directories, string $extension = '.blade.php')
    {
        $this->directories = $directories;
        $this->extension = $extension;
    }

    public function load(string $name): string
    {
        foreach ($this->directories as $directory) {
            $path = $directory . $name . $this->extension;

            if (file_exists($path)) {
                $templateString = @file_get_contents($path);

                if ($templateString === false) {
                    throw new \RuntimeException('Failed to load template: ' . $path);
                }

                return $templateString;
            }
        }
        throw new \RuntimeException('Template not found: ' . $name);
    }

    public function exists(string $name): bool
    {
        foreach ($this->directories as $directory) {
            if (file_exists($directory)) {
                return true;
            }
        }
        return false;
    }

    public function getTimestamp(string $name): int
    {
        foreach ($this->directories as $directory) {
            $path = $directory . $name . $this->extension;

            if (file_exists($path)) {
                $timestamp = filemtime($path);
                return $timestamp !== false ? $timestamp : -1;
            }
        }
        return -1;
    }
}
