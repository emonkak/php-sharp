<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Benchmarks;

use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;

/**
 * @BeforeMethods({"setUp"})
 * @AfterMethods({"tearDown"})
 */
class BladeBench
{
    use TemporaryDirectoryTrait;

    private Factory $factory;

    public function setUp()
    {
        $filesystem = new Filesystem();
        $cacheDirectory = $this->createTemporaryDirectory() . '/';
        $engines = new EngineResolver();
        $engines->register('blade', static function() use ($filesystem, $cacheDirectory) {
            $compiler = new BladeCompiler($filesystem, $cacheDirectory);
            return new CompilerEngine($compiler);
        });
        $finder = new FileViewFinder($filesystem, [__DIR__]);
        $events = new Dispatcher();
        $this->cacheDirectory = $cacheDirectory;
        $this->factory = new Factory($engines, $finder, $events);
    }

    public function tearDown()
    {
        $this->disposeTemporaryDirectories();
    }

    public function benchList()
    {
        $this->factory->make('list', ['size' => 10000])->render();
    }
}
