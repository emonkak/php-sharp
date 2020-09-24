<?php

declare(strict_types=1);

namespace Emonkak\Sharp;

class CompiledTemplate implements TemplateInterface
{
    private string $compiledString;

    public function __construct(string $compiledString)
    {
        $this->compiledString = $compiledString;
    }

    public function render(array $variables): \Iterator
    {
        return (eval($this->compiledString))($variables);
    }

    /**
     * @return callable(array<string,mixed>):\Iterator<string>
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
