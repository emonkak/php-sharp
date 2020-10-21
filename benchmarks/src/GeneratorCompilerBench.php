<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Benchmarks;

use Emonkak\Sharp\Compiler\GeneratorCompiler;
use Emonkak\Sharp\TemplateFactory;

class GeneratorCompilerBench extends AbstractSharpBench
{
    use DataProvider;

    private GeneratorCompiler $compiler;

    private TemplateFactory $factory;

    public function setUp(): void
    {
        $this->compiler = new GeneratorCompiler();
        $this->factory = $this->createTemplateFactory($this->compiler);
    }

    /**
     * @BeforeMethods({"setUp"})
     * @ParamProviders({"provideDataAndMaxChunkSize"})
     * @Warmup(1)
     */
    public function benchRender($params): void
    {
        $this->compiler->setMaxChunkSize($params['maxChunkSize']);

        $chunks = $this->factory
            ->createTemplate($params['template'])
            ->render($params['data']);

        foreach ($chunks as $chunk) {
        }
    }

    public function provideDataAndMaxChunkSize(): \Iterator
    {
        foreach ([0, 1024, 65535] as $maxChunkSize) {
            foreach ($this->provideData() as $key => $params) {
                $key .= '/' . $maxChunkSize;
                yield $key => $params + ['maxChunkSize' => $maxChunkSize];
            }
        }
    }
}
