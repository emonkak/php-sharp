<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Benchmarks;

use Emonkak\Sharp\Cache\FilesystemCache;
use Emonkak\Sharp\Compiler\IteratorBladeCompiler;
use Emonkak\Sharp\Compiler\PhpBladeCompiler;
use Emonkak\Sharp\Compiler\StreamBladeCompiler;
use Emonkak\Sharp\Loader\FilesystemLoader;
use Emonkak\Sharp\TemplateFactory;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;

class SharpBench
{
    private TemplateFactory $factory;

    public function setUpIteratorFactory(): void
    {
        vfsStream::setup();
        $root = vfsStreamWrapper::getRoot();
        $cacheDirectory = vfsStream::url($root->getName() . '/');

        $compiler = new IteratorBladeCompiler();
        $loader = new FilesystemLoader([__DIR__ . '/']);
        $cache = new FilesystemCache($cacheDirectory);
        $this->factory = new TemplateFactory($compiler, $loader, $cache);
    }

    public function setUpStreamFactory(): void
    {
        vfsStream::setup();
        $root = vfsStreamWrapper::getRoot();
        $cacheDirectory = vfsStream::url($root->getName() . '/');

        $compiler = new StreamBladeCompiler();
        $loader = new FilesystemLoader([__DIR__ . '/']);
        $cache = new FilesystemCache($cacheDirectory);
        $this->factory = new TemplateFactory($compiler, $loader, $cache);
    }

    public function setUpPhpFactory(): void
    {
        vfsStream::setup();
        $root = vfsStreamWrapper::getRoot();
        $cacheDirectory = vfsStream::url($root->getName() . '/');

        $compiler = new PhpBladeCompiler();
        $loader = new FilesystemLoader([__DIR__ . '/']);
        $cache = new FilesystemCache($cacheDirectory);
        $this->factory = new TemplateFactory($compiler, $loader, $cache);
    }

    /**
     * @BeforeMethods({"setUpIteratorFactory"})
     */
    public function benchRenderIterator(): void
    {
        $result = $this->factory->getTemplate('list')->render([]);
        $output = fopen('/dev/null', 'w');

        foreach ($result as $chunk) {
            fwrite($output, $chunk);
        }

        fclose($output);
    }

    /**
     * @BeforeMethods({"setUpStreamFactory"})
     */
    public function benchRenderStream(): void
    {
        $input = $this->factory->getTemplate('list')->render([]);
        $output = fopen('/dev/null', 'w');

        rewind($input);

        while (!feof($input)) {
            $chunk = fread($input, 1024 * 8);
            fwrite($output, $chunk);
        }

        fclose($input);
        fclose($output);
    }

    /**
     * @BeforeMethods({"setUpPhpFactory"})
     */
    public function benchRenderPhp(): void
    {
        ob_start();
        $this->factory->getTemplate('list')->render([]);
        $contents = ob_get_clean();
        $output = fopen('/dev/null', 'w');
        fwrite($output, $contents);
    }
}
