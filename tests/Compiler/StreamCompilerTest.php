<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Tests\Compiler;

use Emonkak\Sharp\Compiler\CompilerInterface;
use Emonkak\Sharp\Compiler\StreamCompiler;

class StreamCompilerTest extends AbstractCompilerTest
{
    /**
     * @dataProvider provideTemplates
     */
    public function testCompile(string $name, array $variables, string $expectedString): void
    {
        $result = $this->factory->createTemplate($name)->render($variables);
        rewind($result);
        $this->assertSame($expectedString, stream_get_contents($result));
    }

    protected function createCompiler(): CompilerInterface
    {
        return new StreamCompiler();
    }
}
