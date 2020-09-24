<?php

declare(strict_types=1);

namespace Emonkak\Sharp;

interface TemplateInterface
{
    /**
     * @param array<string,mixed> $variables
     * @return \Iterator<string>
     */
    public function render(array $variables): \Iterator;

    /**
     * @return callable(array<string,mixed>):\Iterator<string>
     */
    public function getRenderer(): callable;

    public function getCompiledString(): string;
}
