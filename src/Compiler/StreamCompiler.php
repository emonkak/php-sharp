<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Compiler;

use Emonkak\Sharp\Loader\LoaderInterface;

/**
 * @extends AbstractCompiler<resource>
 */
class StreamCompiler extends AbstractCompiler
{
    private string $filename;

    private string $mode;

    public function __construct(string $filename = 'php://memory', string $mode = 'r+')
    {
        $this->filename = $filename;
        $this->mode = $mode;
    }

    protected function compileSource(string $body, CompilerContext $context): string
    {
        $filename = var_export($this->filename, true);
        $mode = var_export($this->mode, true);
        return <<<EOL
<?php return static function(\$__variables) {
\$__variables += ['__sections' => [], '__stacks' => [], '__contents' => fopen($filename, $mode)];
extract(\$__variables, EXTR_SKIP | EXTR_REFS);
$body
return \$__contents;
};
EOL;
    }

    protected function compileStatement(string $name, string $parameters, LoaderInterface $loader, CompilerContext $context): string
    {
        $statement = parent::compileStatement($name, $parameters, $loader, $context);
        return $statement . "\n";
    }

    protected function compileEcho(string $expression, CompilerContext $context): string
    {
        return "fwrite(\$__contents, $expression);\n";
    }

    protected function compileYield(string $expression, CompilerContext $context): string
    {
        return $expression . ";\n";
    }
}
