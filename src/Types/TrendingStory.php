<?php

namespace Maximeb97\GoogleTrends\Types;

class TrendingStory
{
    public string $title;
    public string $traffic;
    public ?TrendingImage $image = null;
    /** @var TrendingArticle[] */
    public array $articles;
    public string $shareUrl;

    public function __construct(array $data = [])
    {
        $this->title = $data['title'] ?? '';
        $this->traffic = $data['traffic'] ?? '';
        
        if (isset($data['image'])) {
            $this->image = is_array($data['image']) 
                ? new TrendingImage($data['image']) 
                : $data['image'];
        }
        
        $this->articles = [];
        if (isset($data['articles']) && is_array($data['articles'])) {
            foreach ($data['articles'] as $article) {
                $this->articles[] = is_array($article) 
                    ? new TrendingArticle($article) 
                    : $article;
            }
        }
        
        $this->shareUrl = $data['shareUrl'] ?? '';
    }

    public function toArray(): array
    {
        $result = [
            'title' => $this->title,
            'traffic' => $this->traffic,
            'articles' => array_map(fn($article) => $article->toArray(), $this->articles),
            'shareUrl' => $this->shareUrl,
        ];

        if ($this->image !== null) {
            $result['image'] = $this->image->toArray();
        }

        return $result;
    }
}
