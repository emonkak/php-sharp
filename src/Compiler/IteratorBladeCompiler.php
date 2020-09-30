<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Compiler;

/**
 * @extends AbstractBladeCompiler<\Iterator<string>>
 */
class IteratorBladeCompiler extends AbstractBladeCompiler
{
    protected function compileSource(string $body): string
    {
        return "<?php return static function(\$__variables) { \$__variables += ['__sections' => [], '__stacks' => []]; extract(\$__variables, EXTR_SKIP | EXTR_REFS); $body };";
    }

    protected function compileEcho(string $expression): string
    {
        return "yield $expression;";
    }

    protected function compileYield(string $expression): string
    {
        return "yield from $expression;";
    }
}
