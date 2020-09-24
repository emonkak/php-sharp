<?php

declare(strict_types=1);

namespace Emonkak\Sharp;

interface TemplateEngineInterface
{
    public function getTemplate(string $name): TemplateInterface;

    public function exists(string $name): bool;

    public function getTimestamp(string $name): int;
}
