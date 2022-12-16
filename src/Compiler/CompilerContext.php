<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Compiler;

use Emonkak\Sharp\Loader\LoaderInterface;

class CompilerContext
{
    private LoaderInterface $loader;

    /**
     * @var string[]
     */
    private array $ancestors = [];

    /**
     * @var array<string, string>
     */
    private array $partials = [];

    public function __construct(LoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    public function loadPartial(string $path, CompilerInterface $compiler): string
    {
        if (!isset($this->partials[$path])) {
            $templateString = $this->loader->load($path);
            $body = $compiler->compileBody($templateString, $this);
            $this->partials[$path] = $body;
        }
        return $this->partials[$path];
    }

    /**
     * @return string[]
     */
    public function getAncestors(): array
    {
        return $this->ancestors;
    }

    public function appendParent(string $parent): void
    {
        $this->ancestors[] = $parent;
    }
}
