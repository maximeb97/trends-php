<?php

namespace Maximeb97\GoogleTrends;

use Maximeb97\GoogleTrends\Helpers\Request;
use Maximeb97\GoogleTrends\Helpers\Format;
use Maximeb97\GoogleTrends\Errors\RateLimitError;
use Maximeb97\GoogleTrends\Errors\InvalidRequestError;
use Maximeb97\GoogleTrends\Errors\NetworkError;
use Maximeb97\GoogleTrends\Errors\ParseError;
use Maximeb97\GoogleTrends\Errors\UnknownError;
use Maximeb97\GoogleTrends\Types\GoogleTrendsEndpoints;
use Maximeb97\GoogleTrends\Types\DailyTrendingTopics;
use Maximeb97\GoogleTrends\Types\RelatedTopic;
use Maximeb97\GoogleTrends\Types\RelatedQuery;

class GoogleTrendsApi
{
    /**
     * Get autocomplete suggestions for a keyword
     * 
     * @param string $keyword The keyword to get suggestions for
     * @param string $hl Language code (default: 'en-US')
     * @return array Response with 'data' or 'error'
     */
    public function autocomplete(string $keyword, string $hl = 'en-US'): array
    {
        if (empty($keyword)) {
            return ['data' => []];
        }

        $mapper = Constants::getGoogleTrendsMapper();
        $options = $mapper[GoogleTrendsEndpoints::AUTOCOMPLETE];
        $options['qs'] = [
            'hl' => $hl,
            'tz' => '240',
        ];
        $options['method'] = 'GET';

        try {
            $response = Request::request(
                $options['url'] . '/' . urlencode($keyword),
                $options
            );
            $text = $response['text']();
            
            // Remove the first 5 characters (JSONP wrapper) and parse
            $data = json_decode(substr($text, 5), true);
            
            $topics = array_map(
                function($topic) {
                    return $topic['title'];
                },
                $data['default']['topics'] ?? []
            );
            
            return ['data' => $topics];
        } catch (\Exception $error) {
            return ['error' => new NetworkError($error->getMessage())];
        }
    }

    /**
     * Get daily trending topics
     * 
     * @param array $options Options for daily trends request
     * @return array Response with 'data' or 'error'
     */
    public function dailyTrends(array $options = []): array
    {
        $geo = $options['geo'] ?? 'US';
        $lang = $options['lang'] ?? 'en';

        $mapper = Constants::getGoogleTrendsMapper();
        $defaultOptions = $mapper[GoogleTrendsEndpoints::DAILY_TRENDS];

        $requestOptions = $defaultOptions;
        $requestOptions['body'] = http_build_query([
            'f.req' => sprintf(
                '[[["i0OFE","[null,null,\\"%s\\",0,\\"%s\\",24,1]",null,"generic"]]]',
                $geo,
                $lang
            ),
        ]);
        $requestOptions['contentType'] = 'form';

        try {
            $response = Request::request($defaultOptions['url'], $requestOptions);
            $text = $response['text']();
            $trendingTopics = Format::extractJsonFromResponse($text);

            if (!$trendingTopics) {
                return ['error' => new ParseError()];
            }

            return ['data' => $trendingTopics];
        } catch (ParseError $error) {
            return ['error' => $error];
        } catch (\Exception $error) {
            return ['error' => new NetworkError($error->getMessage())];
        }
    }

    /**
     * Get real-time trending topics
     * 
     * @param array $options Options for real-time trends request
     * @return array Response with 'data' or 'error'
     */
    public function realTimeTrends(array $options = []): array
    {
        $geo = $options['geo'] ?? 'US';
        $trendingHours = $options['trendingHours'] ?? 4;

        $mapper = Constants::getGoogleTrendsMapper();
        $defaultOptions = $mapper[GoogleTrendsEndpoints::DAILY_TRENDS];

        $requestOptions = $defaultOptions;
        $requestOptions['body'] = http_build_query([
            'f.req' => sprintf(
                '[[["i0OFE","[null,null,\\"%s\\",0,\\"en\\",%d,1]",null,"generic"]]]',
                $geo,
                $trendingHours
            ),
        ]);
        $requestOptions['contentType'] = 'form';

        try {
            $response = Request::request($defaultOptions['url'], $requestOptions);
            $text = $response['text']();
            $trendingTopics = Format::extractJsonFromResponse($text);

            if (!$trendingTopics) {
                return ['error' => new ParseError()];
            }

            return ['data' => $trendingTopics];
        } catch (ParseError $error) {
            return ['error' => $error];
        } catch (\Exception $error) {
            return ['error' => new NetworkError($error->getMessage())];
        }
    }

    /**
     * Get explore widget data for a keyword
     * 
     * @param array $options Options for explore request
     * @return array Response with 'widgets' or 'error'
     */
    public function explore(array $options = []): array
    {
        $keyword = $options['keyword'] ?? '';
        $geo = $options['geo'] ?? 'US';
        $time = $options['time'] ?? 'now 1-d';
        $category = $options['category'] ?? 0;
        $property = $options['property'] ?? '';
        $hl = $options['hl'] ?? 'en-US';

        $mapper = Constants::getGoogleTrendsMapper();
        $exploreOptions = $mapper[GoogleTrendsEndpoints::EXPLORE];
        
        $exploreOptions['qs'] = [
            'hl' => $hl,
            'tz' => '240',
            'req' => json_encode([
                'comparisonItem' => [
                    [
                        'keyword' => $keyword,
                        'geo' => $geo,
                        'time' => $time,
                    ],
                ],
                'category' => $category,
                'property' => $property,
            ]),
        ];
        $exploreOptions['contentType'] = 'form';
        $exploreOptions['method'] = 'GET';

        try {
            $response = Request::request($exploreOptions['url'], $exploreOptions);
            $text = $response['text']();

            // Check if response is HTML (error page)
            if (strpos($text, '<html') !== false || strpos($text, '<!DOCTYPE') !== false) {
                return ['error' => new ParseError('Explore request returned HTML instead of JSON')];
            }

            // Try to parse as JSON
            try {
                // Remove the first 5 characters (JSONP wrapper) and parse
                $data = json_decode(substr($text, 5), true);
                
                // Extract widgets from the response
                if ($data && is_array($data) && count($data) > 0) {
                    $widgets = $data[0] ?? [];
                    return ['widgets' => $widgets];
                }
                
                return ['widgets' => []];
            } catch (\Exception $parseError) {
                return ['error' => new ParseError('Failed to parse explore response as JSON: ' . $parseError->getMessage())];
            }
        } catch (\Exception $error) {
            return ['error' => new NetworkError('Explore request failed: ' . $error->getMessage())];
        }
    }

    /**
     * Get interest by region data
     * 
     * @param array $options Options for interest by region request
     * @return array Response with data or 'error'
     */
    public function interestByRegion(array $options = []): array
    {
        $keyword = $options['keyword'] ?? '';
        $startTime = $options['startTime'] ?? new \DateTime('2004-01-01');
        $endTime = $options['endTime'] ?? new \DateTime();
        $geo = $options['geo'] ?? 'US';
        $resolution = $options['resolution'] ?? 'REGION';
        $hl = $options['hl'] ?? 'en-US';
        $timezone = $options['timezone'] ?? (new \DateTime())->getTimezone()->getOffset(new \DateTime()) / 60;
        $category = $options['category'] ?? 0;

        $formatDate = function(\DateTime $date): string {
            return $date->format('Y-m-d');
        };

        $formatTrendsDate = function(\DateTime $date): string {
            return $date->format('Y-m-d\TH\:\\i\:\\s');
        };

        $getDateRangeParam = function(\DateTime $date) use ($formatTrendsDate): string {
            $yesterday = clone $date;
            $yesterday->modify('-1 day');

            $formattedStart = $formatTrendsDate($yesterday);
            $formattedEnd = $formatTrendsDate($date);

            return "$formattedStart $formattedEnd";
        };

        $exploreResponse = $this->explore([
            'keyword' => is_array($keyword) ? $keyword[0] : $keyword,
            'geo' => is_array($geo) ? $geo[0] : $geo,
            'time' => $getDateRangeParam($startTime) . ' ' . $getDateRangeParam($endTime),
            'category' => $category,
            'hl' => $hl,
        ]);

        if (isset($exploreResponse['error'])) {
            return ['error' => $exploreResponse['error']];
        }

        $widget = null;
        foreach ($exploreResponse['widgets'] as $w) {
            if (($w['id'] ?? '') === 'GEO_MAP') {
                $widget = $w;
                break;
            }
        }

        if (!$widget) {
            return ['error' => new ParseError('No GEO_MAP widget found in explore response')];
        }

        $mapper = Constants::getGoogleTrendsMapper();
        $mapperOptions = $mapper[GoogleTrendsEndpoints::INTEREST_BY_REGION];
        
        $mapperOptions['qs'] = [
            'hl' => $hl,
            'tz' => (string)$timezone,
            'req' => json_encode([
                'geo' => [
                    'country' => is_array($geo) ? $geo[0] : $geo,
                ],
                'comparisonItem' => [[
                    'time' => $formatDate($startTime) . ' ' . $formatDate($endTime),
                    'complexKeywordsRestriction' => [
                        'keyword' => [[
                            'type' => 'BROAD',
                            'value' => is_array($keyword) ? $keyword[0] : $keyword,
                        ]],
                    ],
                ]],
                'resolution' => $resolution,
                'locale' => $hl,
                'requestOptions' => [
                    'property' => '',
                    'backend' => 'CM',
                    'category' => $category,
                ],
                'userConfig' => [
                    'userType' => 'USER_TYPE_LEGIT_USER',
                ],
            ]),
            'token' => $widget['token'] ?? '',
        ];
        $mapperOptions['method'] = 'GET';

        try {
            $response = Request::request($mapperOptions['url'], $mapperOptions);
            $text = $response['text']();
            
            // Remove the first 5 characters (JSONP wrapper) and parse
            $data = json_decode(substr($text, 5), true);
            return $data;
        } catch (\Exception $error) {
            return ['error' => new ParseError('Failed to parse interest by region response: ' . $error->getMessage())];
        }
    }

    /**
     * Get related topics for a keyword
     * 
     * @param array $options Options for related topics request
     * @return array Response with 'data' or 'error'
     */
    public function relatedTopics(array $options = []): array
    {
        $keyword = $options['keyword'] ?? '';
        $geo = $options['geo'] ?? 'US';
        $time = $options['time'] ?? 'now 1-d';
        $category = $options['category'] ?? 0;
        $property = $options['property'] ?? '';
        $hl = $options['hl'] ?? 'en-US';

        try {
            // Validate keyword
            if (empty(trim($keyword))) {
                return ['error' => new InvalidRequestError('Keyword is required')];
            }

            // Step 1: Call explore to get widget data and token
            $exploreResponse = $this->explore([
                'keyword' => $keyword,
                'geo' => $geo,
                'time' => $time,
                'category' => $category,
                'property' => $property,
                'hl' => $hl,
            ]);

            if (isset($exploreResponse['error'])) {
                return ['error' => $exploreResponse['error']];
            }

            if (empty($exploreResponse['widgets'])) {
                return ['error' => new ParseError('No widgets found in explore response. This might be due to Google blocking the request, invalid parameters, or network issues.')];
            }

            // Step 2: Find the related topics widget or use any available widget
            $relatedTopicsWidget = null;
            foreach ($exploreResponse['widgets'] as $widget) {
                if (
                    ($widget['id'] ?? '') === 'RELATED_TOPICS' ||
                    (($widget['request']['restriction']['complexKeywordsRestriction']['keyword'][0]['value'] ?? '') === $keyword)
                ) {
                    $relatedTopicsWidget = $widget;
                    break;
                }
            }

            if (!$relatedTopicsWidget) {
                $relatedTopicsWidget = $exploreResponse['widgets'][0]; // Fallback to first widget
            }

            if (!$relatedTopicsWidget) {
                return ['error' => new ParseError('No related topics widget found in explore response')];
            }

            // Step 3: Call the related topics API
            $mapper = Constants::getGoogleTrendsMapper();
            $mapperOptions = $mapper[GoogleTrendsEndpoints::RELATED_TOPICS];
            
            $mapperOptions['qs'] = [
                'hl' => $hl,
                'tz' => '240',
                'req' => json_encode([
                    'restriction' => [
                        'geo' => ['country' => $geo],
                        'time' => $time,
                        'originalTimeRangeForExploreUrl' => $time,
                        'complexKeywordsRestriction' => [
                            'keyword' => [[
                                'type' => 'BROAD',
                                'value' => $keyword,
                            ]],
                        ],
                    ],
                    'keywordType' => 'ENTITY',
                    'metric' => ['TOP', 'RISING'],
                    'trendinessSettings' => [
                        'compareTime' => $time,
                    ],
                    'requestOptions' => [
                        'property' => $property,
                        'backend' => 'CM',
                        'category' => $category,
                    ],
                    'language' => explode('-', $hl)[0],
                    'userCountryCode' => $geo,
                    'userConfig' => [
                        'userType' => 'USER_TYPE_LEGIT_USER',
                    ],
                ]),
            ];

            if (isset($relatedTopicsWidget['token'])) {
                $mapperOptions['qs']['token'] = $relatedTopicsWidget['token'];
            }

            $mapperOptions['method'] = 'GET';

            $response = Request::request($mapperOptions['url'], $mapperOptions);
            $text = $response['text']();

            // Parse the response
            try {
                $data = json_decode(substr($text, 5), true);
                
                // Return the data in the expected format
                return [
                    'data' => [
                        'default' => [
                            'rankedList' => $data['default']['rankedList'] ?? [],
                        ],
                    ],
                ];
            } catch (\Exception $parseError) {
                return ['error' => new ParseError('Failed to parse related topics response: ' . $parseError->getMessage())];
            }
        } catch (\Exception $error) {
            return ['error' => new NetworkError($error->getMessage())];
        }
    }

    /**
     * Get related queries for a keyword
     * 
     * @param array $options Options for related queries request
     * @return array Response with 'data' or 'error'
     */
    public function relatedQueries(array $options = []): array
    {
        $keyword = $options['keyword'] ?? '';
        $geo = $options['geo'] ?? 'US';
        $time = $options['time'] ?? 'now 1-d';
        $hl = $options['hl'] ?? 'en-US';

        try {
            // Validate keyword
            if (empty(trim($keyword))) {
                return ['error' => new ParseError()];
            }

            $autocompleteResult = $this->autocomplete($keyword, $hl);

            if (isset($autocompleteResult['error'])) {
                return ['error' => $autocompleteResult['error']];
            }

            $suggestions = array_slice($autocompleteResult['data'] ?? [], 0, 10);
            $relatedQueries = [];

            foreach ($suggestions as $index => $suggestion) {
                $relatedQueries[] = [
                    'query' => $suggestion,
                    'value' => 100 - $index * 10,
                    'formattedValue' => (string)(100 - $index * 10),
                    'hasData' => true,
                    'link' => '/trends/explore?q=' . urlencode($suggestion) . '&date=' . $time . '&geo=' . $geo,
                ];
            }

            return [
                'data' => [
                    'default' => [
                        'rankedList' => [[
                            'rankedKeyword' => $relatedQueries,
                        ]],
                    ],
                ],
            ];
        } catch (\Exception $error) {
            return ['error' => new NetworkError($error->getMessage())];
        }
    }

    /**
     * Get combined related data (topics + queries)
     * 
     * @param array $options Options for related data request
     * @return array Response with 'data' or 'error'
     */
    public function relatedData(array $options = []): array
    {
        $keyword = $options['keyword'] ?? '';
        $geo = $options['geo'] ?? 'US';
        $time = $options['time'] ?? 'now 1-d';
        $hl = $options['hl'] ?? 'en-US';

        try {
            // Validate keyword
            if (empty(trim($keyword))) {
                return ['error' => new ParseError()];
            }

            $autocompleteResult = $this->autocomplete($keyword, $hl);

            if (isset($autocompleteResult['error'])) {
                return ['error' => $autocompleteResult['error']];
            }

            $suggestions = array_slice($autocompleteResult['data'] ?? [], 0, 10);

            $topics = [];
            $queries = [];

            foreach ($suggestions as $index => $suggestion) {
                $topics[] = [
                    'topic' => [
                        'mid' => '/m/' . $index,
                        'title' => $suggestion,
                        'type' => 'Topic',
                    ],
                    'value' => 100 - $index * 10,
                    'formattedValue' => (string)(100 - $index * 10),
                    'hasData' => true,
                    'link' => '/trends/explore?q=' . urlencode($suggestion) . '&date=' . $time . '&geo=' . $geo,
                ];

                $queries[] = [
                    'query' => $suggestion,
                    'value' => 100 - $index * 10,
                    'formattedValue' => (string)(100 - $index * 10),
                    'hasData' => true,
                    'link' => '/trends/explore?q=' . urlencode($suggestion) . '&date=' . $time . '&geo=' . $geo,
                ];
            }

            return [
                'data' => [
                    'topics' => $topics,
                    'queries' => $queries,
                ],
            ];
        } catch (\Exception $error) {
            return ['error' => new NetworkError($error->getMessage())];
        }
    }
}
