<?php

namespace Maximeb97\GoogleTrends\Types;

class TrendingTopic
{
    public string $title;
    public string $traffic;
    /** @var TrendingArticle[] */
    public array $articles;

    public function __construct(array $data = [])
    {
        $this->title = $data['title'] ?? '';
        $this->traffic = $data['traffic'] ?? '';
        
        $this->articles = [];
        if (isset($data['articles']) && is_array($data['articles'])) {
            foreach ($data['articles'] as $article) {
                $this->articles[] = is_array($article) 
                    ? new TrendingArticle($article) 
                    : $article;
            }
        }
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'traffic' => $this->traffic,
            'articles' => array_map(fn($article) => $article->toArray(), $this->articles),
        ];
    }
}
