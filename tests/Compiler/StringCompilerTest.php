<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Tests\Compiler;

use Emonkak\Sharp\Compiler\CompilerInterface;
use Emonkak\Sharp\Compiler\StringCompiler;

class StringCompilerTest extends AbstractCompilerTest
{
    /**
     * @dataProvider provideTemplates
     */
    public function testCompile(string $name, array $variables, string $expectedString): void
    {
        $result = $this->factory->createTemplate($name)->render($variables);
        $this->assertSame($expectedString, $result);
    }

    protected function createCompiler(): CompilerInterface
    {
        return new StringCompiler();
    }
}
