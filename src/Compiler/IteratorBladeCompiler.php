<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Compiler;

use Emonkak\Sharp\Loader\LoaderInterface;

/**
 * @extends AbstractBladeCompiler<\Iterator<string>>
 */
class IteratorBladeCompiler extends AbstractBladeCompiler
{
    protected function compileSource(string $body): string
    {
        return "<?php return static function(\$__variables) { \$__variables += ['__sections' => [], '__stacks' => []]; extract(\$__variables, EXTR_SKIP | EXTR_REFS); $body };";
    }

    protected function compileStatement(string $name, string $parameters, LoaderInterface $loader, array &$cache, array &$parents): string
    {
        $statement = parent::compileStatement($name, $parameters, $loader, $cache, $parents);
        return $statement . "\n";
    }

    protected function compileEcho(string $expression): string
    {
        return "yield $expression;\n";
    }

    protected function compileYield(string $expression): string
    {
        return "yield from $expression;\n";
    }
}
