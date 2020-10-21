<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Compiler;

use Emonkak\Sharp\Loader\LoaderInterface;

/**
 * @extends AbstractCompiler<void>
 */
class PhpCompiler extends AbstractCompiler
{
    protected function compileSource(string $body, CompilerContext $context): string
    {
        return <<<EOL
<?php return static function(\$__variables) {
\$__variables += ['__sections' => [], '__stacks' => []];
extract(\$__variables, EXTR_SKIP | EXTR_REFS);
?>$body<?php }; ?>
EOL;
    }

    protected function compileStatement(string $name, string $parameters, LoaderInterface $loader, CompilerContext $context): string
    {
        $statement = parent::compileStatement($name, $parameters, $loader, $context);
        if ($statement !== '' && $name !== 'include') {
            $statement = "<?php $statement ?>";
        }
        return $statement;
    }

    protected function compileExtract(string $variables, CompilerContext $context): string
    {
        return '<?php ' . parent::compileExtract($variables, $context) . ' ?>';
    }

    protected function compileConstant(string $constantString, CompilerContext $context): string
    {
        return $constantString[0] === "\n" ? "\n" . $constantString : $constantString;
    }

    protected function compileEcho(string $expression, CompilerContext $context): string
    {
        return "<?php echo $expression; ?>";
    }

    protected function compileYield(string $expression, CompilerContext $context): string
    {
        return $expression . ';';
    }
}
