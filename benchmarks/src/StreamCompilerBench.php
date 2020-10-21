<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Benchmarks;

use Emonkak\Sharp\Compiler\StreamCompiler;
use Emonkak\Sharp\TemplateFactory;

class StreamCompilerBench extends AbstractSharpBench
{
    use DataProvider;

    private TemplateFactory $factory;

    public function setUp(): void
    {
        $compiler = new StreamCompiler();
        $this->factory = $this->createTemplateFactory($compiler);
    }

    /**
     * @BeforeMethods({"setUp"})
     * @ParamProviders({"provideData"})
     * @Warmup(1)
     */
    public function benchRender($params): void
    {
        $stream = $this->factory
            ->createTemplate($params['template'])
            ->render($params['data']);

        rewind($stream);

        while (!feof($stream)) {
            fread($stream, 1024 * 8);
        }

        fclose($stream);
    }
}
