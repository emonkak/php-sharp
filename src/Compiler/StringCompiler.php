<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Compiler;

/**
 * @extends AbstractCompiler<string>
 */
class StringCompiler extends AbstractCompiler
{
    protected function compileSource(string $body, CompilerContext $context): string
    {
        return <<<EOL
<?php return static function(\$__variables) {
\$__variables += ['__sections' => [], '__stacks' => [], '__contents' => ''];
extract(\$__variables, EXTR_SKIP | EXTR_REFS);
$body
return \$__contents;
};
EOL;
    }

    protected function compileEcho(string $expression, CompilerContext $context): string
    {
        return "\$__contents .= $expression;\n";
    }

    protected function compileYield(string $expression, CompilerContext $context): string
    {
        return $expression . ";\n";
    }
}
