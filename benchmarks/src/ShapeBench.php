<?php

declare(strict_types=1);

namespace Emonkak\Shape\Benchmarks;

use Emonkak\Sharp\CachedEngine;
use Emonkak\Sharp\TemplateEngineInterface;
use Emonkak\Sharp\Compiler\BladeCompiler;
use Emonkak\Sharp\Loader\FilesystemLoader;

/**
 * @BeforeMethods({"setUp"})
 * @AfterMethods({"tearDown"})
 */
class ShapeBench
{
    private string $cacheDirectory;

    private TemplateEngineInterface $engine;

    public function setUp()
    {
        $compiler = new BladeCompiler();
        $loader = new FilesystemLoader(__DIR__ . '/');
        $this->cacheDirectory = $this->createTemporaryDirectory();
        $this->engine = new CachedEngine($compiler, $loader, $this->cacheDirectory);
    }

    public function tearDown()
    {
        foreach (new \DirectoryIterator($this->cacheDirectory) as $file) {
            if ($file->isFile()) {
                unlink($file->getPath());
            }
        }
        rmdir($this->cacheDirectory);
    }

    public function benchList()
    {
        $result = $this->engine->getRenderer('list')(['size' => 10000]);
        foreach ($result as $chunk) {
        }
    }

    private function createTemporaryDirectory(int $mode = 0700): string
    {
        do {
            $directory = sys_get_temp_dir() . '/' . uniqid();
        } while (@mkdir($directory, $mode) === false);
        return $directory;
    }
}
