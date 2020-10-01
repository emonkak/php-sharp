<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Benchmarks;

use Emonkak\Sharp\Cache\FilesystemCache;
use Emonkak\Sharp\Compiler\CompilerInterface;
use Emonkak\Sharp\Compiler\IteratorBladeCompiler;
use Emonkak\Sharp\Compiler\PhpBladeCompiler;
use Emonkak\Sharp\Compiler\StreamBladeCompiler;
use Emonkak\Sharp\Compiler\StringBladeCompiler;
use Emonkak\Sharp\Loader\FilesystemLoader;
use Emonkak\Sharp\TemplateFactory;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;

class SharpBench
{
    use SizesProvider;

    private TemplateFactory $factory;

    public function setUpIteratorCompilerFactory(): void
    {
        $compiler = new IteratorBladeCompiler();
        $this->factory = $this->createTemplateFactory($compiler);
    }

    public function setUpStreamCompilerFactory(): void
    {
        $compiler = new StreamBladeCompiler();
        $this->factory = $this->createTemplateFactory($compiler);
    }

    public function setUpPhpCompilerFactory(): void
    {
        $compiler = new PhpBladeCompiler();
        $this->factory = $this->createTemplateFactory($compiler);
    }

    public function setUpStringCompilerFactory(): void
    {
        $compiler = new StringBladeCompiler();
        $this->factory = $this->createTemplateFactory($compiler);
    }

    /**
     * @BeforeMethods({"setUpIteratorCompilerFactory"})
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
     * @BeforeMethods({"setUpStreamCompilerFactory"})
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
     * @BeforeMethods({"setUpPhpCompilerFactory"})
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

    /**
     * @BeforeMethods({"setUpStringCompilerFactory"})
     * @ParamProviders({"provideSizes"})
     */
    public function benchRenderString($params): void
    {
        $result = $this->factory->getTemplate('list')->render(['size' => $params['size']]);
        $output = fopen('/dev/null', 'w');
        fwrite($output, $result);
    }

    private function createTemplateFactory(CompilerInterface $compiler): TemplateFactory
    {
        vfsStream::setup();
        $root = vfsStreamWrapper::getRoot();
        $cacheDirectory = vfsStream::url($root->getName() . '/');

        $loader = new FilesystemLoader([__DIR__ . '/']);
        $cache = new FilesystemCache($cacheDirectory);
        return new TemplateFactory($compiler, $loader, $cache);
    }
}
