<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Benchmarks;

require __DIR__ . '/../vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * @BeforeMethods({"setUp"})
 */
class TwigBench
{
    use SizesProvider;

    private Environment $twig;

    public function setUp()
    {
        $loader = new FilesystemLoader(__DIR__);
        $this->twig = new Environment($loader, [
            'cache' => __DIR__ . '/../cache/twig',
        ]);
    }

    /**
     * @ParamProviders({"provideSizes"})
     */
    public function benchRender($params)
    {
        $result = $this->twig->render('list.twig', ['size' => $params['size']]);
        $output = fopen('/dev/null', 'w');
        fwrite($output, $result);
        fclose($output);
    }
}
