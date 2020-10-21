<?php

declare(strict_types=1);

namespace Emonkak\Sharp\Benchmarks;

trait DataProvider
{
    public function provideData(): \Iterator
    {
        $article = [
            'title' => 'What is Lorem Ipsum?',
            'description' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.',
            'date' => '1970-01-01',
            'tags' => ['php', 'web', 'benchmark'],
            'url' => 'https://example.com',
        ];
        yield 'articles' => [
            'template' => 'articles',
            'data' => ['articles' => array_fill(0, 5000, $article)],
        ];
        yield 'list' => [
            'template' => 'list',
            'data' => ['size' => 100000],
        ];
    }
}
