<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Benchmarks;

trait SizesProvider
{
    public function provideSizes(): array
    {
        return [
            1 => ['size' => 1],
            10 => ['size' => 10],
            100 => ['size' => 100],
            1000 => ['size' => 1000],
            10000 => ['size' => 10000],
            100000 => ['size' => 100000],
            1000000 => ['size' => 1000000],
        ];
    }
}
