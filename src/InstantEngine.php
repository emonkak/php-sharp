<?php

declare(strict_types=1);

namespace Emonkak\Sharp;

use Emonkak\Sharp\Compiler\CompilerInterface;
use Emonkak\Sharp\Loader\LoaderInterface;

class InstantEngine implements TemplateEngineInterface
{
    private CompilerInterface $compiler;

    private LoaderInterface $loader;

    public function __construct(CompilerInterface $compiler, LoaderInterface $loader)
    {
        $this->compiler = $compiler;
        $this->loader = $loader;
    }

    /**
     * @return callable(array<string,mixed>):\Iterator<string>
     */
    public function getRenderer(string $name): callable
    {
        $templateString = $this->loader->load($name);
        $compiledString = $this->compiler->compile($templateString, $this->loader);
        return eval($compiledString);
    }

    public function exists(string $name): bool
    {
        return $this->loader->exists($name);
    }
}
