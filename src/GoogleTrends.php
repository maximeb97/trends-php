<?php

namespace Maximeb97\GoogleTrends;

/**
 * Google Trends API for PHP
 * 
 * This class provides a simple interface to interact with the Google Trends API.
 * All methods are static and delegate to a shared GoogleTrendsApi instance.
 */
class GoogleTrends
{
    private static ?GoogleTrendsApi $api = null;

    /**
     * Get the shared API instance
     */
    private static function getApi(): GoogleTrendsApi
    {
        if (self::$api === null) {
            self::$api = new GoogleTrendsApi();
        }
        return self::$api;
    }

    /**
     * Get daily trending topics
     * 
     * @param array $options Options for daily trends request
     *                       - geo: string (default: 'US')
     *                       - lang: string (default: 'en')
     * @return array Response with 'data' or 'error'
     */
    public static function dailyTrends(array $options = []): array
    {
        return self::getApi()->dailyTrends($options);
    }

    /**
     * Get real-time trending topics
     * 
     * @param array $options Options for real-time trends request
     *                       - geo: string (default: 'US')
     *                       - trendingHours: int (default: 4)
     * @return array Response with 'data' or 'error'
     */
    public static function realTimeTrends(array $options = []): array
    {
        return self::getApi()->realTimeTrends($options);
    }

    /**
     * Get autocomplete suggestions for a keyword
     * 
     * @param string $keyword The keyword to get suggestions for
     * @param string $hl Language code (default: 'en-US')
     * @return array Response with 'data' or 'error'
     */
    public static function autocomplete(string $keyword, string $hl = 'en-US'): array
    {
        return self::getApi()->autocomplete($keyword, $hl);
    }

    /**
     * Get explore widget data for a keyword
     * 
     * @param array $options Options for explore request
     *                       - keyword: string (required)
     *                       - geo: string (default: 'US')
     *                       - time: string (default: 'now 1-d')
     *                       - category: int (default: 0)
     *                       - property: string (default: '')
     *                       - hl: string (default: 'en-US')
     * @return array Response with 'widgets' or 'error'
     */
    public static function explore(array $options = []): array
    {
        return self::getApi()->explore($options);
    }

    /**
     * Get interest by region data
     * 
     * @param array $options Options for interest by region request
     *                       - keyword: string|array (required)
     *                       - startTime: DateTime (default: 2004-01-01)
     *                       - endTime: DateTime (default: now)
     *                       - geo: string|array (default: 'US')
     *                       - resolution: string (default: 'REGION')
     *                       - hl: string (default: 'en-US')
     *                       - timezone: int (default: current timezone offset)
     *                       - category: int (default: 0)
     * @return array Response with data or 'error'
     */
    public static function interestByRegion(array $options = []): array
    {
        return self::getApi()->interestByRegion($options);
    }

    /**
     * Get related topics for a keyword
     * 
     * @param array $options Options for related topics request
     *                       - keyword: string (required)
     *                       - geo: string (default: 'US')
     *                       - time: string (default: 'now 1-d')
     *                       - category: int (default: 0)
     *                       - property: string (default: '')
     *                       - hl: string (default: 'en-US')
     * @return array Response with 'data' or 'error'
     */
    public static function relatedTopics(array $options = []): array
    {
        return self::getApi()->relatedTopics($options);
    }

    /**
     * Get related queries for a keyword
     * 
     * @param array $options Options for related queries request
     *                       - keyword: string (required)
     *                       - geo: string (default: 'US')
     *                       - time: string (default: 'now 1-d')
     *                       - hl: string (default: 'en-US')
     * @return array Response with 'data' or 'error'
     */
    public static function relatedQueries(array $options = []): array
    {
        return self::getApi()->relatedQueries($options);
    }

    /**
     * Get combined related data (topics + queries)
     * 
     * @param array $options Options for related data request
     *                       - keyword: string (required)
     *                       - geo: string (default: 'US')
     *                       - time: string (default: 'now 1-d')
     *                       - hl: string (default: 'en-US')
     * @return array Response with 'data' or 'error'
     */
    public static function relatedData(array $options = []): array
    {
        return self::getApi()->relatedData($options);
    }
}
