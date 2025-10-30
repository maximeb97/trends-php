<?php

namespace Maximeb97\GoogleTrends\Types;

class DailyTrendingTopics
{
    /** @var TrendingStory[] */
    public array $allTrendingStories;
    /** @var TrendingTopic[] */
    public array $summary;

    public function __construct(array $data = [])
    {
        $this->allTrendingStories = [];
        if (isset($data['allTrendingStories']) && is_array($data['allTrendingStories'])) {
            foreach ($data['allTrendingStories'] as $story) {
                $this->allTrendingStories[] = is_array($story) 
                    ? new TrendingStory($story) 
                    : $story;
            }
        }

        $this->summary = [];
        if (isset($data['summary']) && is_array($data['summary'])) {
            foreach ($data['summary'] as $topic) {
                $this->summary[] = is_array($topic) 
                    ? new TrendingTopic($topic) 
                    : $topic;
            }
        }
    }

    public function toArray(): array
    {
        return [
            'allTrendingStories' => array_map(fn($story) => $story->toArray(), $this->allTrendingStories),
            'summary' => array_map(fn($topic) => $topic->toArray(), $this->summary),
        ];
    }
}
