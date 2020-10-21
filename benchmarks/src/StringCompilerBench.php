<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Benchmarks;

use Emonkak\Sharp\Compiler\StringCompiler;
use Emonkak\Sharp\TemplateFactory;

class StringCompilerBench extends AbstractSharpBench
{
    use DataProvider;

    private TemplateFactory $factory;

    public function setUp(): void
    {
        $compiler = new StringCompiler();
        $this->factory = $this->createTemplateFactory($compiler);
    }

    /**
     * @BeforeMethods({"setUp"})
     * @ParamProviders({"provideData"})
     * @Warmup(1)
     */
    public function benchRender($params): void
    {
        $this->factory
            ->createTemplate($params['template'])
            ->render($params['data']);
    }
}
