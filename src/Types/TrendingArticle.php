<?php

namespace Maximeb97\GoogleTrends\Types;

class TrendingArticle
{
    public string $title;
    public string $url;
    public string $source;
    public string $time;
    public string $snippet;

    public function __construct(array $data = [])
    {
        $this->title = $data['title'] ?? '';
        $this->url = $data['url'] ?? '';
        $this->source = $data['source'] ?? '';
        $this->time = $data['time'] ?? '';
        $this->snippet = $data['snippet'] ?? '';
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'url' => $this->url,
            'source' => $this->source,
            'time' => $this->time,
            'snippet' => $this->snippet,
        ];
    }
}
