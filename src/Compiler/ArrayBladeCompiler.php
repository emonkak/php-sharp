<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Compiler;

use Emonkak\Sharp\Loader\LoaderInterface;

/**
 * @extends AbstractBladeCompiler<string[]>
 */
class ArrayBladeCompiler extends AbstractBladeCompiler
{
    protected function compileSource(string $body): string
    {
        return "<?php return static function(\$__variables) { \$__variables += ['__sections' => [], '__stacks' => [], '__contents' => []]; extract(\$__variables, EXTR_SKIP | EXTR_REFS); $body; return \$__contents; };";
    }

    protected function compileEcho(string $expression): string
    {
        return "\$__contents[] = $expression;\n";
    }

    protected function compileYield(string $expression): string
    {
        return $expression . ";\n";
    }
}
