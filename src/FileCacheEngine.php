<?php

declare(strict_types=1);

namespace Emonkak\Sharp;

/**
 * @template T
 */
class FileCacheEngine implements TemplateEngineInterface
{
    /**
     * @var TemplateEngineInterface<T>
     */
    private TemplateEngineInterface $engine;

    private string $cacheDirectory;

    /**
     * @param TemplateEngineInterface<T> $engine
     */
    public function __construct(TemplateEngineInterface $engine, string $cacheDirectory)
    {
        $this->engine = $engine;
        $this->cacheDirectory = $cacheDirectory;
    }

    /**
     * @return TemplateInterface<T>
     */
    public function getTemplate(string $name): TemplateInterface
    {
        $cachePath = $this->cacheDirectory . sha1($name) . '.php';
        $isExpired = (!file_exists($cachePath)
                      || $this->engine->getTimestamp($name) >= filemtime($cachePath));

        if ($isExpired) {
            $template = $this->engine->getTemplate($name);
            $source = '<?php ' . $template->getCompiledString();

            if (@file_put_contents($cachePath, $source) === false) {
                throw new \RuntimeException('Unable to save the cache: ' . $cachePath);
            }
        }

        return new FileTemplate($cachePath);
    }

    public function exists(string $name): bool
    {
        return $this->engine->exists($name);
    }

    public function getTimestamp(string $name): int
    {
        return $this->engine->getTimestamp($name);
    }
}
