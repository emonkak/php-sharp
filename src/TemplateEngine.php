<?php

declare(strict_types=1);

namespace Emonkak\Sharp;

use Emonkak\Sharp\Compiler\CompilerInterface;
use Emonkak\Sharp\Loader\LoaderInterface;

/**
 * @template T
 */
class TemplateEngine implements TemplateEngineInterface
{
    /**
     * @var CompilerInterface<T>
     */
    private CompilerInterface $compiler;

    private LoaderInterface $loader;

    /**
     * @param CompilerInterface<T> $compiler
     */
    public function __construct(CompilerInterface $compiler, LoaderInterface $loader)
    {
        $this->compiler = $compiler;
        $this->loader = $loader;
    }

    /**
     * @return TemplateInterface<T>
     */
    public function getTemplate(string $name): TemplateInterface
    {
        $templateString = $this->loader->load($name);
        return $this->compiler->compile($templateString, $this->loader);
    }

    public function exists(string $name): bool
    {
        return $this->loader->exists($name);
    }

    public function getTimestamp(string $name): int
    {
        return $this->loader->getTimestamp($name);
    }

    /**
     * @return TemplateEngineInterface<T>
     */
    public function withFileCache(string $cacheDirectory): TemplateEngineInterface
    {
        return new FileCacheEngine($this, $cacheDirectory);
    }
}
