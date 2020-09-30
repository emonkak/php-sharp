<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Loader;

class FilesystemLoader implements LoaderInterface
{
    /**
     * @var string[]
     */
    private array $templateDirectories;

    private string $extension;

    /**
     * @param string[] $templateDirectories
     */
    public function __construct(array $templateDirectories, string $extension = '.blade.php')
    {
        $this->templateDirectories = $templateDirectories;
        $this->extension = $extension;
    }

    public function load(string $name): string
    {
        $path = $this->findTemplate($name);
        if ($path === null) {
            throw new \RuntimeException('Template file does not exist: ' . $name);
        }

        $templateString = @file_get_contents($path);
        if ($templateString === false) {
            throw new \RuntimeException('Failed to load template: ' . $path);
        }

        return $templateString;
    }

    public function exists(string $name): bool
    {
        return $this->findTemplate($name) !== null;
    }

    public function getTimestamp(string $name): int
    {
        $path = $this->findTemplate($name);
        if ($path === null) {
            return -1;
        }

        $timestamp = filemtime($path);
        if ($timestamp === false) {
            return -1;
        }

        return $timestamp;
    }

    public function isFresh(string $name, int $timestamp): bool
    {
        $path = $this->findTemplate($name);
        if ($path === null) {
            return false;
        }
        $templateTimestamp = filemtime($path);
        if ($templateTimestamp === false) {
            return false;
        }
        return $templateTimestamp < $timestamp;
    }

    private function findTemplate(string $name): ?string
    {
        foreach ($this->templateDirectories as $templateDirectory) {
            $path = $templateDirectory . $name . $this->extension;

            if (file_exists($path)) {
                return $path;
            }
        }
        return null;
    }
}
