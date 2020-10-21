<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Benchmarks;

use Emonkak\Sharp\Compiler\ArrayCompiler;
use Emonkak\Sharp\TemplateFactory;

class ArrayCompilerBench extends AbstractSharpBench
{
    use DataProvider;

    private TemplateFactory $factory;

    public function setUp(): void
    {
        $compiler = new ArrayCompiler();
        $this->factory = $this->createTemplateFactory($compiler);
    }

    /**
     * @BeforeMethods({"setUp"})
     * @ParamProviders({"provideData"})
     * @Warmup(1)
     */
    public function benchRender($params): void
    {
        $chunks = $this->factory
            ->createTemplate($params['template'])
            ->render($params['data']);

        foreach ($chunks as $chunk) {
        }
    }
}
