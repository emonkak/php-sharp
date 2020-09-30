<?php

declare(strict_types=1);

namespace Emonkak\Sharp;

use Emonkak\Sharp\Cache\CacheInterface;
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
    public function getTemplate(string $name): TemplateInterface
    {
        $key = $this->compiler->generateKey($name);
        $template = $this->getCompiledTemplate($key);

        if ($template === null) {
            $templateString = $this->loader->load($name);
            $template = $this->compiler->compile($templateString, $this->loader);
            $this->cache->write($key, $template);
        }

        return $template;
    }

    /**
     * @return ?TemplateInterface<T>
     */
    private function getCompiledTemplate(string $key): ?TemplateInterface
    {
        if (!$this->cache->exists($key)) {
            return null;
        }

        $timestamp = $this->cache->getTimestamp($key);
        if (!$this->loader->isFresh($key, $timestamp)) {
            return null;
        }

        return $this->cache->load($key);
    }
}
