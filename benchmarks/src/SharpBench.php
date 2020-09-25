<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Benchmarks;

use Emonkak\Sharp\Compiler\IteratorBladeCompiler;
use Emonkak\Sharp\Compiler\StreamBladeCompiler;
use Emonkak\Sharp\Loader\FilesystemLoader;
use Emonkak\Sharp\TemplateEngine;
use Emonkak\Sharp\TemplateEngineInterface;

/**
 * @AfterMethods({"tearDown"})
 */
class SharpBench
{
    use TemporaryDirectoryTrait;

    private TemplateEngineInterface $engine;

    public function setUpIteratorEngine()
    {
        $compiler = new IteratorBladeCompiler();
        $loader = new FilesystemLoader([__DIR__ . '/']);
        $cacheDirectory = $this->createTemporaryDirectory() . '/';
        $this->engine = (new TemplateEngine($compiler, $loader))->withFileCache($cacheDirectory);
    }

    public function setUpStreamEngine()
    {
        $compiler = new StreamBladeCompiler();
        $loader = new FilesystemLoader([__DIR__ . '/']);
        $cacheDirectory = $this->createTemporaryDirectory() . '/';
        $this->engine = (new TemplateEngine($compiler, $loader))->withFileCache($cacheDirectory);
    }

    public function tearDown()
    {
        $this->disposeTemporaryDirectories();
    }

    /**
     * @BeforeMethods({"setUpIteratorEngine"})
     */
    public function benchRenderIterator()
    {
        $result = $this->engine->getTemplate('list')->render(['size' => 10000]);
        foreach ($result as $chunk) {
        }
    }

    /**
     * @BeforeMethods({"setUpStreamEngine"})
     */
    public function benchRenderStream()
    {
        $this->engine->getTemplate('list')->render(['size' => 10000]);
    }
}
