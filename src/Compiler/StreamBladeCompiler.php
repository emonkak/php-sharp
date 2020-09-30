<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Compiler;

/**
 * @extends AbstractBladeCompiler<resource>
 */
class StreamBladeCompiler extends AbstractBladeCompiler
{
    protected function compileSource(string $body): string
    {
        return "<?php return static function(\$__variables) { \$__variables += ['__sections' => [], '__stacks' => [], '__stream' => fopen('php://memory', 'r+')]; extract(\$__variables, EXTR_SKIP | EXTR_REFS); $body; return \$__stream; };";
    }

    protected function compileEcho(string $expression): string
    {
        return "fwrite(\$__stream, $expression);";
    }

    protected function compileYield(string $expression): string
    {
        return $expression . ';';
    }
}
