<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Compiler;

/**
 * @extends AbstractBladeCompiler<resource>
 */
class StreamBladeCompiler extends AbstractBladeCompiler
{
    protected function wrapBody(string $body): string
    {
        return "return static function(\$__variables) { \$__sections = []; \$__stacks = []; \$__stream = fopen('php://memory', 'r+'); extract(\$__variables, EXTR_SKIP); $body; return \$__stream; };";
    }

    protected function captureVariables(): string
    {
        return "\$__variables, \$__sections, \$__stacks, \$__stream";
    }

    protected function yield(string $expression): string
    {
        return "fwrite(\$__stream, $expression);";
    }

    protected function yieldFrom(string $expression): string
    {
        return $expression . ';';
    }
}
