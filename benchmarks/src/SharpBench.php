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
    use SizesProvider;

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
     * @ParamProviders({"provideSizes"})
     */
    public function benchRenderIterator($params): void
    {
        $result = $this->factory->getTemplate('list')->render(['size' => $params['size']]);
        $output = fopen('/dev/null', 'w');

        foreach ($result as $chunk) {
            fwrite($output, $chunk);
        }

        fclose($output);
    }

    /**
     * @BeforeMethods({"setUpStreamFactory"})
     * @ParamProviders({"provideSizes"})
     */
    public function benchRenderStream($params): void
    {
        $input = $this->factory->getTemplate('list')->render(['size' => $params['size']]);
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
     * @ParamProviders({"provideSizes"})
     */
    public function benchRenderPhp($params): void
    {
        ob_start();
        $this->factory->getTemplate('list')->render(['size' => $params['size']]);
        $contents = ob_get_clean();
        $output = fopen('/dev/null', 'w');
        fwrite($output, $contents);
    }
}
