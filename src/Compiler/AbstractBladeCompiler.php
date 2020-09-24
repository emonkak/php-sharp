<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Compiler;

use Emonkak\Sharp\CompiledTemplate;
use Emonkak\Sharp\Loader\LoaderInterface;
use Emonkak\Sharp\TemplateInterface;

/**
 * @template T
 * @implements CompilerInterface<T>
 */
abstract class AbstractBladeCompiler implements CompilerInterface
{
    const FORM_PATTERN = '/{{--(.*?)--}}|{{\s*(.+?)\s*}}|{!!\s*(.+?)\s*!!}|\B@(@?\w+)(?:\s*(\(((?>[^()]+)|(?5))*\)))?/s';

    /**
     * @return TemplateInterface<T>
     */
    public function compile(string $templateString, LoaderInterface $loader): TemplateInterface
    {
        $cache = [];
        $body = $this->compileBody($templateString, $loader, $cache);
        $wrappedBody = $this->wrapBody($body);
        return new CompiledTemplate($wrappedBody);
    }

    abstract protected function wrapBody(string $body): string;

    abstract protected function yield(string $expression): string;

    abstract protected function yieldFrom(string $expression): string;

    private function compileBody(string $templateString, LoaderInterface $loader, array &$cache): string
    {
        $constants = $this->extractConstants($templateString);
        $forms = $this->extractForms($templateString);

        $body = '';
        $parents = [];

        for ($i = 0, $l = count($forms); $i < $l; $i++) {
            $body .= $this->compileConstants($constants[$i]);
            $body .= $this->compileForm($forms[$i], $loader, $cache, $parents);
        }

        for ($l = count($constants); $i < $l; $i++) {
            $body .= $this->compileConstants($constants[$i]);
        }

        foreach ($parents as $parent) {
            $body .= $parent;
        }

        return $body;
    }

    private function compileConstants(string $constantString): string
    {
        if ($constantString === '') {
            return '';
        }
        return $this->yield(var_export($constantString, true)) . PHP_EOL;
    }

    /**
     * @param array<array-key,mixed> $cache
     * @param string[] $parents
     */
    private function compileForm(array $matches, LoaderInterface $loader, array &$cache, array &$parents): string
    {
        if (isset($matches[4])) {  // Statement
            return $this->compileStatement($matches[4], $matches[5] ?? '', $loader, $cache, $parents) . PHP_EOL;
        }
        if (isset($matches[3])) {  // Unescaped Data
            return $this->yield($matches[3]) . PHP_EOL;
        }
        if (isset($matches[2])) {  // Escaped Data
            return $this->yield('htmlspecialchars(' . $matches[2] . ', ENT_QUOTES, "UTF-8", false)') . PHP_EOL;
        }
        // Comment
        return '';
    }

    /**
     * @param array<array-key,mixed> $cache
     * @param string[] $parents
     */
    private function compileStatement(string $name, string $parameters, LoaderInterface $loader, array &$cache, array &$parents): string
    {
        switch ($name) {
            case 'if':
                return "if $parameters:";
            case 'elseif':
                return "elseif $parameters:";
            case 'for':
                return "for $parameters:";
            case 'foreach':
                return "foreach $parameters:";
            case 'switch':
                return "switch $parameters:";
            case 'case':
                $expression = $this->stripParentheses($parameters);
                return "case $expression:";
            case 'include':
                list($path, $variables) = $this->unquote($parameters);
                if (isset($cache[$path])) {
                    return $cache[$path];
                }
                $templateString = $loader->load($path);
                $body = $this->compileBody($templateString, $loader, $cache);
                if ($variables !== '') {
                    $body = "extract($variables, EXTR_SKIP);" . PHP_EOL . $body;
                }
                $cache[$path] = $body;
                return $body;
            case 'extends':
                list($path) = $this->unquote($parameters);
                if (isset($cache[$path])) {
                    return $cache[$path];
                }
                $templateString = $loader->load($path);
                $body = $this->compileBody($templateString, $loader, $cache);
                $parents[] = $body;
                $cache[$path] = $body;
                return '';
            case 'section':
                $name = $this->stripParentheses($parameters);
                $captureVariables = $this->captureVariables();
                return "\$__sections[$name] = function() use ($captureVariables) { extract(\$__variables, EXTR_SKIP);";
            case 'push':
                $name = $this->stripParentheses($parameters);
                $captureVariables = $this->captureVariables();
                return "\$__stacks[$name][] = function() use ($captureVariables) { extract(\$__variables, EXTR_SKIP);";
            case 'hasSection':
                $name = $this->stripParentheses($parameters);
                return "if (isset(\$__sections[$name])):";
            case 'yield':
                $name = $this->stripParentheses($parameters);
                return $this->yieldFrom("\$__sections[$name]()");
            case 'stack':
                $name = $this->stripParentheses($parameters);
                return "if (isset(\$__stacks[$name])) foreach (\$__stacks[$name] as \$__stack) " . $this->yieldFrom('$__stack()');
            case 'else':
                return 'else:';
            case 'default':
                return 'default:';
            case 'break':
                return 'break;';
            case 'continue':
                return 'continue;';
            case 'endif':
                return 'endif;';
            case 'endfor':
                return 'endfor;';
            case 'endforeach':
                return 'endforeach;';
            case 'endwhile':
                return 'endwhile;';
            case 'endswitch':
                return 'endswitch;';
            case 'endsection':
            case 'endpush':
                return '};';
            default:
                if (isset($name[0]) && $name[0] === '@') {
                    return $this->yield(var_export($name . $parameters, true));
                }
                throw new \RuntimeException('Unknown statement: @' . $name . $parameters);
        }
    }

    /**
     * @return string[]
     */
    private function extractConstants(string $templateString): array
    {
        $constants = preg_split(self::FORM_PATTERN, $templateString);
        return $constants !== false ? $constants : [];
    }

    /**
     * @return string[][]
     */
    private function extractForms(string $templateString): array
    {
        /** @psalm-var int|false */
        $result = preg_match_all(self::FORM_PATTERN, $templateString, $matches, PREG_SET_ORDER);
        return $result !== false ? $matches : [];
    }

    /**
     * @return array{0:string,1:string}
     */
    private function unquote(string $input): array
    {
        $result = preg_match('/^\(\s*([\'"])(.*?)\1\s*(?:,\s*(.+?))?\)$/s', $input, $matches);
        if ($result !== false && !empty($matches)) {
            return [$matches[2], $matches[3] ?? ''];
        }
        return ['', ''];
    }

    public function stripParentheses(string $input): string
    {
        if (isset($input[0]) && $input[0] === '(') {
            return substr($input, 1, -1);
        }
        return $input;
    }
}
