<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Compiler;

use Emonkak\Sharp\Loader\LoaderInterface;

/**
 * @extends AbstractBladeCompiler<void>
 */
class PhpBladeCompiler extends AbstractBladeCompiler
{
    protected function compileSource(string $body): string
    {
        return "<?php return static function(\$__variables) { \$__variables += ['__sections' => [], '__stacks' => []]; extract(\$__variables, EXTR_SKIP | EXTR_REFS); ?>$body<?php };";
    }

    protected function compileStatement(string $name, string $parameters, LoaderInterface $loader, array &$cache, array &$parents): string
    {
        $statement = parent::compileStatement($name, $parameters, $loader, $cache, $parents);
        if ($statement !== '' && $name !== 'include') {
            $statement = "<?php $statement ?>";
        }
        return $statement;
    }

    protected function compileExtract(string $variables): string
    {
        return '<?php ' .  parent::compileExtract($variables) . ' ?>';
    }

    protected function compileConstants(string $constantString): string
    {
        if ($constantString[0] === "\n") {
            $constantString = "\n" . $constantString;
        }
        return $constantString;
    }

    protected function compileEcho(string $expression): string
    {
        return "<?php echo $expression; ?>";
    }

    protected function compileYield(string $expression): string
    {
        return $expression . ';';
    }
}
