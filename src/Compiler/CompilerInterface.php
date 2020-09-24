<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Compiler;

use Emonkak\Sharp\Loader\LoaderInterface;
use Emonkak\Sharp\TemplateInterface;

/**
 * @template T
 */
interface CompilerInterface
{
    /**
     * @return TemplateInterface<T>
     */
    public function compile(string $templateString, LoaderInterface $loader): TemplateInterface;
}
