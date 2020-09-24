<?php

declare(strict_types=1);

namespace Emonkak\Sharp;

/**
 * @template T
 * @implements TemplateInterface<T>
 */
class CompiledTemplate implements TemplateInterface
{
    private string $compiledString;

    public function __construct(string $compiledString)
    {
        $this->compiledString = $compiledString;
    }

    /**
     * @return T
     */
    public function render(array $variables)
    {
        return (eval($this->compiledString))($variables);
    }

    /**
     * @return callable(array<string,mixed>):T
     */
    public function getRenderer(): callable
    {
        return eval($this->compiledString);
    }

    public function getCompiledString(): string
    {
        return $this->compiledString;
    }
}
