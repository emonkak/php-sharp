<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Compiler;

/**
 * @extends AbstractBladeCompiler<\Iterator<string>>
 */
class IteratorBladeCompiler extends AbstractBladeCompiler
{
    protected function wrapBody(string $body): string
    {
        return "return static function(\$__variables) { \$__sections = []; \$__stacks = []; extract(\$__variables, EXTR_SKIP); $body };";
    }

    protected function captureVariables(): string
    {
        return "\$__variables, \$__sections, \$__stacks";
    }

    protected function yield(string $expression): string
    {
        return "yield $expression;";
    }

    protected function yieldFrom(string $expression): string
    {
        return "yield from $expression;";
    }
}
