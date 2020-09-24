<?php

declare(strict_types=1);

namespace Emonkak\Sharp;

/**
 * @template T
 */
interface TemplateInterface
{
    /**
     * @param array<string,mixed> $variables
     * @return T
     */
    public function render(array $variables);

    /**
     * @return callable(array<string,mixed>):T
     */
    public function getRenderer(): callable;

    public function getCompiledString(): string;
}
