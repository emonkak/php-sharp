<?php

declare(strict_types=1);

namespace Emonkak\Sharp;

use Emonkak\Sharp\Compiler\CompilerInterface;
use Emonkak\Sharp\Loader\LoaderInterface;

class CachedEngine implements TemplateEngineInterface
{
    private CompilerInterface $compiler;

    private LoaderInterface $loader;

    private string $cacheDirectory;

    public function __construct(CompilerInterface $compiler, LoaderInterface $loader, string $cacheDirectory)
    {
        $this->compiler = $compiler;
        $this->loader = $loader;
        $this->cacheDirectory = $cacheDirectory;
    }

    /**
     * @return callable(array<string,mixed>):\Iterator<string>
     */
    public function getRenderer(string $name): callable
    {
        $cachePath = $this->cacheDirectory . sha1($name) . '.php';

        if ($this->isExpired($name, $cachePath)) {
            $templateString = $this->loader->load($name);
            $compiledString = $this->compiler->compile($templateString, $this->loader);
            $source = '<?php ' . $compiledString;
            if (@\file_put_contents($cachePath, $source) === false) {
                throw new \RuntimeException('Unable to write the cache: ' . $cachePath);
            }
        }

        return require $cachePath;
    }

    public function exists(string $name): bool
    {
        return $this->loader->exists($name);
    }

    public function isExpired(string $name, string $cachePath)
    {
        if (!\file_exists($cachePath)) {
            return true;
        }

        return $this->loader->getTimestamp($name) >= \filemtime($cachePath);
    }
}
