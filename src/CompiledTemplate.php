<?php

declare(strict_types=1);

namespace Emonkak\Sharp;

/**
 * @template T
 * @implements TemplateInterface<T>
 */
class CompiledTemplate implements TemplateInterface
{
    private string $source;

    public function __construct(string $source)
    {
        $this->source = $source;
    }

    /**
     * @param array<string, mixed> $variables
     * @return T
     */
    public function render(array $variables)
    {
        return (eval('?>' . $this->source))($variables);
    }

    /**
     * @return callable(array<string, mixed>):T
     */
    public function getRenderer(): callable
    {
        return eval('?>' . $this->source);
    }

    public function getSource(): string
    {
        return $this->source;
    }
}
