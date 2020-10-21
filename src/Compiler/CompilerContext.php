<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Compiler;

class CompilerContext
{
    /**
     * @var string[]
     */
    private array $parents = [];

    /**
     * @var array<string, string>
     */
    private array $partials = [];

    public function getPartial(string $name): string
    {
        return $this->partials[$name];
    }

    /**
     * @return string[]
     */
    public function getParents(): array
    {
        return $this->parents;
    }

    public function hasPartial(string $name): bool
    {
        return isset($this->partials[$name]);
    }

    public function appendParent(string $parent): void
    {
        $this->parents[] = $parent;
    }

    public function addPartial(string $name, string $partial): void
    {
        $this->partials[$name] = $partial;
    }
}
