<?php

declare(strict_types=1);

namespace Emonkak\Shape\Benchmarks;

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
    private string $cacheDirectory;

    private Factory $factory;

    public function setUp()
    {
        $filesystem = new Filesystem();
        $cacheDirectory = $this->createTemporaryDirectory();
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
        foreach (new \DirectoryIterator($this->cacheDirectory) as $file) {
            if ($file->isFile()) {
                unlink($file->getPath());
            }
        }
        rmdir($this->cacheDirectory);
    }

    public function benchList()
    {
        $this->factory->make('list', ['size' => 10000])->render();
    }

    private function createTemporaryDirectory(int $mode = 0700): string
    {
        do {
            $directory = sys_get_temp_dir() . '/' . uniqid();
        } while (@mkdir($directory, $mode) === false);
        return $directory;
    }
}
