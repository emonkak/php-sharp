<?php

declare(strict_types=1);

namespace Emonkak\Sharp;

class FileTemplate implements TemplateInterface
{
    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function render(array $variables): \Iterator
    {
        /** @psalm-suppress UnresolvableInclude */
        return (require $this->path)($variables);
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
