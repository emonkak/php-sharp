<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Loader;

class InMemoryLoader implements LoaderInterface
{
    /**
     * @var array<string,string>
     */
    private array $templates;

    /**
     * @param array<string,string>
     */
    public function __construct(array $templates = [])
    {
        $this->templates = $templates;
    }

    public function exists(string $name): bool
    {
        return isset($this->templates[$name]);
    }

    public function load(string $name): string
    {
        return $this->templates[$name] ?? '';
    }

    public function getTimestamp(string $name): int
    {
        return \PHP_INT_MAX;
    }
}
