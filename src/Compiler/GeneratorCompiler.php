<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Compiler;

/**
 * @extends AbstractCompiler<\Generator<string>>
 */
class GeneratorCompiler extends AbstractCompiler
{
    private int $maxChunkSize;

    public function __construct(int $maxChunkSize = 65535)
    {
        $this->maxChunkSize = $maxChunkSize;
    }

    public function setMaxChunkSize(int $maxChunkSize): void
    {
        $this->maxChunkSize = $maxChunkSize;
    }

    protected function compileSource(string $body, CompilerContext $context): string
    {
        return <<<EOL
<?php return static function(\$__variables) {
\$__variables += ['__sections' => [], '__stacks' => [], '__buffer' => ''];
extract(\$__variables, EXTR_SKIP | EXTR_REFS);
$body
if (\$__buffer !== '') yield \$__buffer;
};
EOL;
    }

    protected function compileConstant(string $constantString, CompilerContext $context): string
    {
        $expression = var_export($constantString, true);
        $maxChunkSize = $this->maxChunkSize;
        return "\$__buffer .= $expression; if (strlen(\$__buffer) > $maxChunkSize) { yield \$__buffer; \$__buffer = ''; }\n";
    }

    protected function compileEcho(string $expression, CompilerContext $context): string
    {
        return "\$__buffer .= $expression;\n";
    }

    protected function compileYield(string $expression, CompilerContext $context): string
    {
        return "yield from $expression;\n";
    }
}
