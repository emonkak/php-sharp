<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Loader;

class InMemoryLoader implements LoaderInterface
{
    /**
     * @var array<string, string>
     */
    private array $templates;

    /**
     * @param array<string, string> $templates
     */
    public function __construct(array $templates = [])
    {
        $this->templates = $templates;
    }

    public function load(string $name): string
    {
        return $this->templates[$name] ?? '';
    }

    public function exists(string $name): bool
    {
        return isset($this->templates[$name]);
    }

    public function isFresh(string $name, int $timestamp): bool
    {
        return true;
    }
}
