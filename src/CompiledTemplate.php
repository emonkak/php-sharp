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

    public function getCompiledString(): string
    {
        return $this->compiledString;
    }
}
