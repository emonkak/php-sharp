<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Tests\Compiler;

use Emonkak\Sharp\Compiler\CompilerInterface;
use Emonkak\Sharp\Compiler\PhpCompiler;

class PhpCompilerTest extends AbstractCompilerTest
{
    /**
     * @dataProvider provideTemplates
     */
    public function testCompile(string $name, array $variables, string $expectedString): void
    {
        ob_start();
        $this->factory->createTemplate($name)->render($variables);
        $result = ob_get_clean();
        $this->assertSame($expectedString, $result);
    }

    protected function createCompiler(): CompilerInterface
    {
        return new PhpCompiler();
    }
}
