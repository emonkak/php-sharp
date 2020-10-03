<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Compiler;

use Emonkak\Sharp\Loader\LoaderInterface;

/**
 * @extends AbstractBladeCompiler<\Iterator<string>>
 */
class IteratorBladeCompiler extends AbstractBladeCompiler
{
    private int $chunkSize;

    public function __construct(int $chunkSize = 65535)
    {
        $this->chunkSize = $chunkSize;
    }

    protected function compileSource(string $body): string
    {
        return "<?php return static function(\$__variables) { \$__variables += ['__sections' => [], '__stacks' => [], '__buffer' => '', '__bufferSize' => 0]; extract(\$__variables, EXTR_SKIP | EXTR_REFS); $body if (\$__buffer !== '') yield \$__buffer; };";
    }

    protected function compileStatement(string $name, string $parameters, LoaderInterface $loader, array &$cache, array &$parents): string
    {
        $statement = parent::compileStatement($name, $parameters, $loader, $cache, $parents);
        return $statement . "\n";
    }

    protected function compileConstants(string $constantString): string
    {
        if ($constantString === '') {
            return '';
        }
        $expression = var_export($constantString, true);
        $length = strlen($constantString);
        return "\$__buffer .= $expression; \$__bufferSize += $length; if (\$__bufferSize >= $this->chunkSize) { yield \$__buffer; \$__buffer = ''; \$__bufferSize = 0; }\n";
    }

    protected function compileEcho(string $expression): string
    {
        return "\$__buffer .= $expression;\n";
    }

    protected function compileYield(string $expression): string
    {
        return "yield from $expression;\n";
    }
}
