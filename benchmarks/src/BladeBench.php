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
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;

/**
 * @BeforeMethods({"setUp"})
 */
class BladeBench
{
    use SizesProvider;

    private Factory $factory;

    public function setUp()
    {
        vfsStream::setup();
        $root = vfsStreamWrapper::getRoot();
        $cacheDirectory = vfsStream::url($root->getName() . '/');

        $filesystem = new Filesystem();
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

    /**
     * @ParamProviders({"provideSizes"})
     */
    public function benchRender($params)
    {
        $result = $this->factory->make('list', ['size' => $params['size']])->render();
        $output = fopen('/dev/null', 'w');
        fwrite($output, $result);
        fclose($output);
    }
}
