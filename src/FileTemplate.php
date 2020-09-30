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
     * @param array<string,mixed> $variables
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

    public function getSource(): string
    {
        return file_get_contents($this->path);
    }
}
