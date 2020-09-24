<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Benchmarks;

use Emonkak\Sharp\Compiler\BladeCompiler;
use Emonkak\Sharp\Loader\FilesystemLoader;
use Emonkak\Sharp\TemplateEngine;
use Emonkak\Sharp\TemplateEngineInterface;

/**
 * @BeforeMethods({"setUp"})
 * @AfterMethods({"tearDown"})
 */
class SharpBench
{
    use TemporaryDirectoryTrait;

    private TemplateEngineInterface $engine;

    public function setUp()
    {
        $compiler = new BladeCompiler();
        $loader = new FilesystemLoader(__DIR__ . '/');
        $cacheDirectory = $this->createTemporaryDirectory() . '/';
        $this->engine = (new TemplateEngine($compiler, $loader))->withFileCache($cacheDirectory);
    }

    public function tearDown()
    {
        $this->disposeTemporaryDirectories();
    }

    public function benchList()
    {
        $result = $this->engine->getTemplate('list')->render(['size' => 10000]);
        foreach ($result as $chunk) {
        }
    }
}
