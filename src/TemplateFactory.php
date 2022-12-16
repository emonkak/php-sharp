<?php

declare(strict_types=1);

namespace Emonkak\Sharp;

use Emonkak\Sharp\Cache\CacheInterface;
use Emonkak\Sharp\Compiler\CompilerContext;
use Emonkak\Sharp\Compiler\CompilerInterface;
use Emonkak\Sharp\Loader\LoaderInterface;

/**
 * @template T
 */
class TemplateFactory
{
    /**
     * @var CompilerInterface<T>
     */
    private CompilerInterface $compiler;

    private LoaderInterface $loader;

    private CacheInterface $cache;

    /**
     * @param CompilerInterface<T> $compiler
     */
    public function __construct(CompilerInterface $compiler, LoaderInterface $loader, CacheInterface $cache)
    {
        $this->compiler = $compiler;
        $this->loader = $loader;
        $this->cache = $cache;
    }

    /**
     * @return TemplateInterface<T>
     */
    public function createTemplate(string $name): TemplateInterface
    {
        $key = $this->compiler->generateKey($name);
        $template = $this->loadCompiledTemplate($name, $key);

        if ($template === null) {
            $templateString = $this->loader->load($name);
            $context = new CompilerContext($this->loader);
            $template = $this->compiler->compile($templateString, $context);
            $this->cache->write($key, $template);
        }

        return $template;
    }

    /**
     * @return ?TemplateInterface<T>
     */
    private function loadCompiledTemplate(string $name, string $key): ?TemplateInterface
    {
        if (!$this->cache->exists($key)) {
            return null;
        }

        $timestamp = $this->cache->getTimestamp($key);
        if (!$this->loader->isFresh($name, $timestamp)) {
            return null;
        }

        return $this->cache->load($key);
    }
}
