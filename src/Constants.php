<?php

namespace Maximeb97\GoogleTrends;

use Maximeb97\GoogleTrends\Types\GoogleTrendsEndpoints;

class Constants
{
    public const GOOGLE_TRENDS_BASE_URL = 'trends.google.com';

    public static function getGoogleTrendsMapper(): array
    {
        return [
            GoogleTrendsEndpoints::DAILY_TRENDS => [
                'path' => '/_/TrendsUi/data/batchexecute',
                'method' => 'POST',
                'host' => self::GOOGLE_TRENDS_BASE_URL,
                'url' => 'https://' . self::GOOGLE_TRENDS_BASE_URL . '/_/TrendsUi/data/batchexecute',
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8',
                ],
            ],
            GoogleTrendsEndpoints::AUTOCOMPLETE => [
                'path' => '/trends/api/autocomplete',
                'method' => 'GET',
                'host' => self::GOOGLE_TRENDS_BASE_URL,
                'url' => 'https://' . self::GOOGLE_TRENDS_BASE_URL . '/trends/api/autocomplete',
                'headers' => [
                    'accept' => 'application/json, text/plain, */*',
                ],
            ],
            GoogleTrendsEndpoints::EXPLORE => [
                'path' => '/trends/api/explore',
                'method' => 'POST',
                'host' => self::GOOGLE_TRENDS_BASE_URL,
                'url' => 'https://' . self::GOOGLE_TRENDS_BASE_URL . '/trends/api/explore',
                'headers' => [],
            ],
            GoogleTrendsEndpoints::INTEREST_BY_REGION => [
                'path' => '/trends/api/widgetdata/comparedgeo',
                'method' => 'GET',
                'host' => self::GOOGLE_TRENDS_BASE_URL,
                'url' => 'https://' . self::GOOGLE_TRENDS_BASE_URL . '/trends/api/widgetdata/comparedgeo',
                'headers' => [],
            ],
            GoogleTrendsEndpoints::RELATED_TOPICS => [
                'path' => '/trends/api/widgetdata/relatedtopics',
                'method' => 'GET',
                'host' => self::GOOGLE_TRENDS_BASE_URL,
                'url' => 'https://' . self::GOOGLE_TRENDS_BASE_URL . '/trends/api/widgetdata/relatedtopics',
                'headers' => [],
            ],
            GoogleTrendsEndpoints::RELATED_QUERIES => [
                'path' => '/trends/api/widgetdata/relatedqueries',
                'method' => 'GET',
                'host' => self::GOOGLE_TRENDS_BASE_URL,
                'url' => 'https://' . self::GOOGLE_TRENDS_BASE_URL . '/trends/api/widgetdata/relatedqueries',
                'headers' => [],
            ],
        ];
    }
}
