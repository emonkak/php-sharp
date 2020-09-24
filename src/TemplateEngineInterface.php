<?php

declare(strict_types=1);

namespace Emonkak\Sharp;

/**
 * @template T
 */
interface TemplateEngineInterface
{
    /**
     * @return TemplateInterface<T>
     */
    public function getTemplate(string $name): TemplateInterface;

    public function exists(string $name): bool;

    public function getTimestamp(string $name): int;
}
