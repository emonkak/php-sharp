<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Tests\Compiler;

use Emonkak\Sharp\Cache\NullCache;
use Emonkak\Sharp\Compiler\CompilerInterface;
use Emonkak\Sharp\Loader\InMemoryLoader;
use Emonkak\Sharp\TemplateFactory;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;
use PHPUnit\Framework\TestCase;

abstract class AbstractCompilerTest extends TestCase
{
    private const TEMPLATES = [
    'hello_test' => '<p>Hello <strong>{{$name}}</strong>!</p>',
    'if_test' => '@if ($n % 2 === 0) even @else odd @endif',
    'for_test' => <<<'EOL'
@for ($i = 0; $i < $n; $i++)
{{$i}}
@endfor
EOL,
    'foreach_test' => <<<'EOL'
@foreach ($items as $key => $value)
{{$key}}: {{$value}}
@endforeach
EOL,
    'extends_test' => <<<'EOL'
@extends('layout')
@section('body')
Hello World!
@endsection
EOL,
    'layout' => <<<'EOL'
<body>
@yield('body')
</body>
EOL,
];

    protected TemplateFactory $factory;

    public function setUp(): void
    {
        vfsStream::setup();

        $root = vfsStreamWrapper::getRoot();
        $cacheDirectory = vfsStream::url($root->getName() . '/');

        $compiler = $this->createCompiler();
        $loader = new InMemoryLoader(self::TEMPLATES);
        $cache = new NullCache();

        $this->factory = new TemplateFactory($compiler, $loader, $cache);
    }

    public function provideTemplates(): array
    {
        return [
            ['hello_test', ['name' => 'World'], '<p>Hello <strong>World</strong>!</p>'],
            ['if_test', ['n' => 0], ' even '],
            ['if_test', ['n' => 1], ' odd '],
            ['for_test', ['n' => 10], "0\n1\n2\n3\n4\n5\n6\n7\n8\n9\n"],
            [
                'foreach_test',
                ['items' => ['foo' => 'bar', 'bar' => 'baz', 'baz' => 'qux']],
                "foo: bar\nbar: baz\nbaz: qux\n",
            ],
            [
                'extends_test',
                [],
                "<body>\nHello World!\n</body>",
            ],
        ];
    }

    abstract protected function createCompiler(): CompilerInterface;
}
