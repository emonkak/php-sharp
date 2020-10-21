<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Benchmarks;

use Emonkak\Sharp\Compiler\PhpCompiler;
use Emonkak\Sharp\TemplateFactory;

class PhpCompilerBench extends AbstractSharpBench
{
    use DataProvider;

    private TemplateFactory $factory;

    public function setUp(): void
    {
        $compiler = new PhpCompiler();
        $this->factory = $this->createTemplateFactory($compiler);
    }

    /**
     * @BeforeMethods({"setUp"})
     * @ParamProviders({"provideData"})
     * @Warmup(1)
     */
    public function benchRender($params): void
    {
        ob_start();
        $this->factory
            ->createTemplate($params['template'])
            ->render($params['data']);
        ob_get_clean();
    }
}
