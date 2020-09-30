<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Cache;

use Emonkak\Sharp\TemplateInterface;

class NullCache implements CacheInterface
{
    public function load(string $key): ?TemplateInterface
    {
        return null;
    }

    public function write(string $key, TemplateInterface $template): void
    {
    }

    public function exists(string $key): bool
    {
        return false;
    }

    public function getTimestamp(string $key): int
    {
        return -1;
    }
}
