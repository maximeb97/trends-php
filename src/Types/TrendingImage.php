<?php

namespace Maximeb97\GoogleTrends\Types;

class TrendingImage
{
    public string $newsUrl;
    public string $source;
    public string $imageUrl;

    public function __construct(array $data = [])
    {
        $this->newsUrl = $data['newsUrl'] ?? '';
        $this->source = $data['source'] ?? '';
        $this->imageUrl = $data['imageUrl'] ?? '';
    }

    public function toArray(): array
    {
        return [
            'newsUrl' => $this->newsUrl,
            'source' => $this->source,
            'imageUrl' => $this->imageUrl,
        ];
    }
}
