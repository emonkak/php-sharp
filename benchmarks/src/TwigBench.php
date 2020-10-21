<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Benchmarks;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * @BeforeMethods({"setUp"})
 */
class TwigBench
{
    use DataProvider;

    private Environment $twig;

    public function setUp()
    {
        $loader = new FilesystemLoader(__DIR__);
        $this->twig = new Environment($loader, [
            'cache' => __DIR__ . '/../cache/twig',
        ]);
    }

    /**
     * @ParamProviders({"provideData"})
     * @Warmup(1)
     */
    public function benchRender($params)
    {
        $this->twig->render($params['template'] . '.twig', $params['data']);
    }
}
