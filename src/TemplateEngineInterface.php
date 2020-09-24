<?php

declare(strict_types=1);

namespace Emonkak\Sharp;

interface TemplateEngineInterface
{
    /**
     * @return callable(array<string,mixed>):\Iterator<string>
     */
    public function getRenderer(string $name): callable;

    public function exists(string $name): bool;
}
