<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Benchmarks;

use Emonkak\Sharp\Cache\FilesystemCache;
use Emonkak\Sharp\Compiler\CompilerInterface;
use Emonkak\Sharp\Loader\FilesystemLoader;
use Emonkak\Sharp\TemplateFactory;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;

abstract class AbstractSharpBench
{
    protected function createTemplateFactory(CompilerInterface $compiler): TemplateFactory
    {
        vfsStream::setup();
        $root = vfsStreamWrapper::getRoot();
        $cacheDirectory = vfsStream::url($root->getName() . '/');

        $loader = new FilesystemLoader([__DIR__ . '/']);
        $cache = new FilesystemCache($cacheDirectory);
        return new TemplateFactory($compiler, $loader, $cache);
    }
}
