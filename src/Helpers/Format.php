<?php

namespace Maximeb97\GoogleTrends\Helpers;

use Maximeb97\GoogleTrends\Errors\ParseError;
use Maximeb97\GoogleTrends\Types\DailyTrendingTopics;
use Maximeb97\GoogleTrends\Types\TrendingStory;
use Maximeb97\GoogleTrends\Types\TrendingTopic;
use Maximeb97\GoogleTrends\Types\TrendingArticle;
use Maximeb97\GoogleTrends\Types\TrendingImage;

class Format
{
    /**
     * Extract JSON from Google Trends response
     * 
     * @param string $text Raw response text
     * @return DailyTrendingTopics|null Parsed trending topics or null
     * @throws ParseError On parsing failure
     */
    public static function extractJsonFromResponse(string $text): ?DailyTrendingTopics
    {
        // Remove JSONP wrapper
        $cleanedText = preg_replace('/^\)\]\}\'/', '', $text);
        $cleanedText = trim($cleanedText);
        
        try {
            $parsedResponse = json_decode($cleanedText, true);
            
            if (!is_array($parsedResponse) || count($parsedResponse) === 0) {
                throw new ParseError('Invalid response format: empty array');
            }
            
            $nestedJsonString = $parsedResponse[0][2] ?? null;
            
            if (!$nestedJsonString) {
                throw new ParseError('Invalid response format: missing nested JSON');
            }
            
            $data = json_decode($nestedJsonString, true);
            
            if (!$data || !is_array($data) || count($data) < 2) {
                throw new ParseError('Invalid response format: missing data array');
            }
            
            return self::updateResponseObject($data[1]);
        } catch (ParseError $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new ParseError('Failed to parse response');
        }
    }

    /**
     * Update response object to structured format
     * 
     * @param array $data Raw data array
     * @return DailyTrendingTopics Structured trending topics
     * @throws ParseError On invalid data format
     */
    private static function updateResponseObject(array $data): DailyTrendingTopics
    {
        if (!is_array($data)) {
            throw new ParseError('Invalid data format: expected array');
        }

        $allTrendingStories = [];
        $summary = [];

        foreach ($data as $item) {
            if (is_array($item)) {
                $articles = [];
                if (isset($item[9]) && is_array($item[9])) {
                    foreach ($item[9] as $article) {
                        if (is_array($article)) {
                            $articles[] = new TrendingArticle([
                                'title' => (string)($article[0] ?? ''),
                                'url' => (string)($article[1] ?? ''),
                                'source' => (string)($article[2] ?? ''),
                                'time' => (string)($article[3] ?? ''),
                                'snippet' => (string)($article[4] ?? ''),
                            ]);
                        }
                    }
                }

                $storyData = [
                    'title' => (string)($item[0] ?? ''),
                    'traffic' => (string)($item[6] ?? '0'),
                    'articles' => $articles,
                    'shareUrl' => (string)($item[12] ?? ''),
                ];

                if (isset($item[1]) && is_array($item[1])) {
                    $storyData['image'] = new TrendingImage([
                        'newsUrl' => (string)($item[1][0] ?? ''),
                        'source' => (string)($item[1][1] ?? ''),
                        'imageUrl' => (string)($item[1][2] ?? ''),
                    ]);
                }

                $story = new TrendingStory($storyData);
                $allTrendingStories[] = $story;

                $summary[] = new TrendingTopic([
                    'title' => $story->title,
                    'traffic' => $story->traffic,
                    'articles' => $story->articles,
                ]);
            }
        }

        return new DailyTrendingTopics([
            'allTrendingStories' => $allTrendingStories,
            'summary' => $summary,
        ]);
    }
}
