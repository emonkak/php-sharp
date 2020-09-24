<?php

declare(strict_types=1);

namespace Emonkak\Sharp;

/**
 * @template T
 * @implements TemplateInterface<T>
 */
class FileTemplate implements TemplateInterface
{
    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * @return T
     */
    public function render(array $variables)
    {
        /** @psalm-suppress UnresolvableInclude */
        return (require $this->path)($variables);
    }

    /**
     * @return callable(array<string,mixed>):T
     */
    public function getRenderer(): callable
    {
        /** @psalm-suppress UnresolvableInclude */
        return require $this->path;
    }

    public function getCompiledString(): string
    {
        $source = @file_get_contents($this->path);
        if ($source === false) {
            return '';
        }
        if (substr($source, 0, 5) === '<?php') {
            $source = ltrim(substr($source, 5));
        }
        return $source;
    }
}
