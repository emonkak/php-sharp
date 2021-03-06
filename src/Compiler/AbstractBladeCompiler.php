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
    const FORM_PATTERN = '/{{--(.*?)--}}|{{\s*(.+?)\s*}}|{!!\s*(.+?)\s*!!}|\B@(@?\w+)(?:\s*(\((?:(?>[^()]+)|(?5))*\)))?(?: *\n)?/s';

    public function generateKey(string $name): string
    {
        return sha1(static::class . ':' . $name);
    }

    /**
     * @return TemplateInterface<T>
     */
    public function compile(string $templateString, LoaderInterface $loader): TemplateInterface
    {
        $cache = [];
        $body = $this->compileBody($templateString, $loader, $cache);
        $source = $this->compileSource($body);
        return new CompiledTemplate($source);
    }

    abstract protected function compileSource(string $body): string;

    protected function compileBody(string $templateString, LoaderInterface $loader, array &$cache): string
    {
        $constants = $this->extractConstants($templateString);
        $forms = $this->extractForms($templateString);

        $body = '';
        $parents = [];

        for ($i = 0, $l = count($forms); $i < $l; $i++) {
            $constant = $constants[$i];
            if ($constant !== '') {
                $body .= $this->compileConstants($constant);
            }
            $body .= $this->compileForm($forms[$i], $loader, $cache, $parents);
        }

        for ($l = count($constants); $i < $l; $i++) {
            $constant = $constants[$i];
            if ($constant !== '') {
                $body .= $this->compileConstants($constant);
            }
        }

        foreach ($parents as $parent) {
            $body .= $parent;
        }

        return $body;
    }

    /**
     * @param array<array-key,mixed> $cache
     * @param string[] $parents
     */
    protected function compileForm(array $matches, LoaderInterface $loader, array &$cache, array &$parents): string
    {
        if (isset($matches[4])) {  // Statement
            $name = $matches[4];
            $parameters = $matches[5] ?? '';
            if (isset($name[0]) && $name[0] === '@') {
                return $this->compileConstants($matches[0]);
            }
            return $this->compileStatement($name, $parameters, $loader, $cache, $parents);
        }
        if (isset($matches[3])) {  // Unescaped Data
            $expression = $matches[3];
            return $this->compileEcho($expression);
        }
        if (isset($matches[2])) {  // Escaped Data
            $expression = $matches[2];
            return $this->compileEcho("htmlspecialchars($expression, ENT_QUOTES, 'UTF-8', false)");
        }
        // Comment
        return '';
    }

    /**
     * @param array<array-key,mixed> $cache
     * @param string[] $parents
     */
    protected function compileStatement(string $name, string $parameters, LoaderInterface $loader, array &$cache, array &$parents): string
    {
        switch ($name) {
            case 'if':
                return "if $parameters {";
            case 'elseif':
                return "} elseif $parameters {";
            case 'for':
                return "for $parameters {";
            case 'foreach':
                return "foreach $parameters {";
            case 'switch':
                return "switch $parameters {";
            case 'case':
                $expression = $this->stripParentheses($parameters);
                return "case $expression:";
            case 'include':
                list($path, $variables) = $this->unquote($parameters);
                if (isset($cache[$path])) {
                    $body = $cache[$path];
                } else {
                    $templateString = $loader->load($path);
                    $body = $this->compileBody($templateString, $loader, $cache);
                    $cache[$path] = $body;
                }
                if ($variables !== '') {
                    $body = $this->compileExtract($variables) . ' ' . $body;
                }
                return $body;
            case 'extends':
                list($path) = $this->unquote($parameters);
                if (isset($cache[$path])) {
                    $body = $cache[$path];
                } else {
                    $templateString = $loader->load($path);
                    $body = $this->compileBody($templateString, $loader, $cache);
                    $cache[$path] = $body;
                }
                /** @var string $body */
                $parents[] = $body;
                return '';
            case 'section':
                $name = $this->stripParentheses($parameters);
                return "\$__sections[$name] = function() use (\$__variables) { extract(\$__variables, EXTR_SKIP | EXTR_REFS);";
            case 'push':
                $name = $this->stripParentheses($parameters);
                return "\$__stacks[$name][] = function() use (\$__variables) { extract(\$__variables, EXTR_SKIP | EXTR_REFS);";
            case 'hasSection':
                $name = $this->stripParentheses($parameters);
                return "if (isset(\$__sections[$name])) {";
            case 'yield':
                $name = $this->stripParentheses($parameters);
                return "if (isset(\$__sections[$name])) {" . $this->compileYield("\$__sections[$name]()") . '}';
            case 'stack':
                $name = $this->stripParentheses($parameters);
                return "if (isset(\$__stacks[$name])) { foreach (\$__stacks[$name] as \$__stack) { " . $this->compileYield('$__stack()') . '} }';
            case 'else':
                return '} else {';
            case 'default':
                return 'default:';
            case 'break':
                return 'break;';
            case 'continue':
                return 'continue;';
            case 'endif':
            case 'endfor':
            case 'endforeach':
            case 'endwhile':
            case 'endswitch':
            case 'endsection':
            case 'endpush':
                return '};';
            default:
                throw new \RuntimeException('Unknown statement: @' . $name . $parameters);
        }
    }

    protected function compileConstants(string $constantString): string
    {
        return $this->compileEcho(var_export($constantString, true));
    }

    protected function compileExtract(string $variables): string
    {
        return "extract($variables, EXTR_OVERWRITE | EXTR_REFS);";
    }

    abstract protected function compileEcho(string $expression): string;

    abstract protected function compileYield(string $expression): string;

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

    private function stripParentheses(string $input): string
    {
        if (isset($input[0]) && $input[0] === '(') {
            return substr($input, 1, -1);
        }
        return $input;
    }
}
