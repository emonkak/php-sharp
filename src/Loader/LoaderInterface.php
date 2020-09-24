<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Loader;

interface LoaderInterface
{
    public function exists(string $name): bool;

    public function load(string $name): string;

    public function getTimestamp(string $name): int;
}
