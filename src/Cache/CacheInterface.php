<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Cache;

use Emonkak\Sharp\TemplateInterface;

interface CacheInterface
{
    /**
     * @template T
     * @return ?TemplateInterface<T>
     */
    public function load(string $key): ?TemplateInterface;

    /**
     * @template T
     * @param TemplateInterface<T> $template
     */
    public function write(string $key, TemplateInterface $template): void;

    public function exists(string $key): bool;

    public function getTimestamp(string $key): int;
}
