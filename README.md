# Google Trends API for PHP

A PHP library for interacting with the Google Trends API. This package provides a simple and type-safe way to access Google Trends data programmatically.

This is a PHP port of the TypeScript library [@shaivpidadi/trends-js](https://github.com/Shaivpidadi/trends-js), maintaining identical functionality and behavior.

## Installation

```bash
composer require maximeb97/trends-php
```

## Requirements

- PHP >= 7.4
- cURL extension enabled

## Features

- Get daily trending topics
- Get real-time trending topics
- Get autocomplete suggestions
- Explore trends data
- Get interest by region data
- Get related topics for any keyword
- Get related queries for any keyword
- Get combined related data (topics + queries)
- Full error handling with custom exceptions

## Usage

### Basic Usage

```php
<?php

require_once 'vendor/autoload.php';

use Maximeb97\GoogleTrends\GoogleTrends;

// Get daily trends
$result = GoogleTrends::dailyTrends(['geo' => 'US', 'lang' => 'en']);

if (isset($result['data'])) {
    $trends = $result['data'];
    foreach ($trends->allTrendingStories as $story) {
        echo $story->title . " - Traffic: " . $story->traffic . "\n";
    }
} else {
    echo "Error: " . $result['error']->getMessage() . "\n";
}
```

### Daily Trends

Get daily trending topics for a specific region:

```php
use Maximeb97\GoogleTrends\GoogleTrends;

$result = GoogleTrends::dailyTrends([
    'geo' => 'US',  // Default: 'US'
    'lang' => 'en', // Default: 'en'
]);

// Result structure:
// [
//   'data' => DailyTrendingTopics {
//     allTrendingStories: TrendingStory[],
//     summary: TrendingTopic[]
//   }
// ]
// or
// ['error' => GoogleTrendsError]
```

### Real-Time Trends

Get real-time trending topics:

```php
use Maximeb97\GoogleTrends\GoogleTrends;
use Maximeb97\GoogleTrends\Types\GoogleTrendsTrendingHours;

$result = GoogleTrends::realTimeTrends([
    'geo' => 'US', // Default: 'US'
    'trendingHours' => GoogleTrendsTrendingHours::FOUR_HOURS, // Default: 4
]);

// Available trending hours:
// - GoogleTrendsTrendingHours::FOUR_HOURS (4)
// - GoogleTrendsTrendingHours::ONE_DAY (24)
// - GoogleTrendsTrendingHours::TWO_DAYS (48)
// - GoogleTrendsTrendingHours::SEVEN_DAYS (168)
```

### Autocomplete

Get search suggestions for a keyword:

```php
$result = GoogleTrends::autocomplete(
    'bitcoin',  // Keyword to get suggestions for
    'en-US'     // Language (default: 'en-US')
);

// Returns: ['data' => string[]] or ['error' => GoogleTrendsError]
if (isset($result['data'])) {
    print_r($result['data']); // Array of suggestion strings
}
```

### Explore

Get widget data for a keyword:

```php
$result = GoogleTrends::explore([
    'keyword' => 'bitcoin',
    'geo' => 'US',           // Default: 'US'
    'time' => 'today 12-m',  // Default: 'today 12-m'
    'category' => 0,         // Default: 0
    'property' => '',        // Default: ''
    'hl' => 'en-US',        // Default: 'en-US'
]);

// Returns: ['widgets' => array] or ['error' => GoogleTrendsError]
```

### Interest By Region

Get interest data by geographic region:

```php
$result = GoogleTrends::interestByRegion([
    'keyword' => 'bitcoin',
    'startTime' => new DateTime('2023-01-01'), // Default: new DateTime('2004-01-01')
    'endTime' => new DateTime(),               // Default: now
    'geo' => 'US',                            // Default: 'US'
    'resolution' => 'REGION',                 // Default: 'REGION' (can be 'COUNTRY', 'REGION', 'CITY', 'DMA')
    'hl' => 'en-US',                         // Default: 'en-US'
    'timezone' => 0,                          // Default: current timezone offset
    'category' => 0,                          // Default: 0
]);

// Returns: array with geoMapData or ['error' => GoogleTrendsError]
```

### Related Topics

Get related topics for a keyword:

```php
$result = GoogleTrends::relatedTopics([
    'keyword' => 'bitcoin',
    'geo' => 'US',          // Default: 'US'
    'time' => 'now 1-d',    // Default: 'now 1-d'
    'category' => 0,        // Default: 0
    'property' => '',       // Default: ''
    'hl' => 'en-US',       // Default: 'en-US'
]);

// Returns: ['data' => ['default' => ['rankedList' => [...]]]] or ['error' => GoogleTrendsError]
if (isset($result['data'])) {
    $rankedList = $result['data']['default']['rankedList'];
    foreach ($rankedList as $list) {
        foreach ($list['rankedKeyword'] as $topic) {
            echo $topic['topic']['title'] . "\n";
        }
    }
}
```

### Related Queries

Get related queries for a keyword:

```php
$result = GoogleTrends::relatedQueries([
    'keyword' => 'bitcoin',
    'geo' => 'US',          // Default: 'US'
    'time' => 'now 1-d',    // Default: 'now 1-d'
    'hl' => 'en-US',       // Default: 'en-US'
]);

// Returns: ['data' => ['default' => ['rankedList' => [...]]]] or ['error' => GoogleTrendsError]
if (isset($result['data'])) {
    $rankedList = $result['data']['default']['rankedList'];
    foreach ($rankedList as $list) {
        foreach ($list['rankedKeyword'] as $query) {
            echo $query['query'] . " - " . $query['formattedValue'] . "\n";
        }
    }
}
```

### Related Data (Combined)

Get combined related topics and queries:

```php
$result = GoogleTrends::relatedData([
    'keyword' => 'bitcoin',
    'geo' => 'US',          // Default: 'US'
    'time' => 'now 1-d',    // Default: 'now 1-d'
    'hl' => 'en-US',       // Default: 'en-US'
]);

// Returns: ['data' => ['topics' => [...], 'queries' => [...]]] or ['error' => GoogleTrendsError]
if (isset($result['data'])) {
    echo "Related Topics:\n";
    foreach ($result['data']['topics'] as $topic) {
        echo "  - " . $topic['topic']['title'] . "\n";
    }
    
    echo "\nRelated Queries:\n";
    foreach ($result['data']['queries'] as $query) {
        echo "  - " . $query['query'] . "\n";
    }
}
```

## Error Handling

The library uses custom exception classes for different error types:

```php
use Maximeb97\GoogleTrends\Errors\RateLimitError;
use Maximeb97\GoogleTrends\Errors\InvalidRequestError;
use Maximeb97\GoogleTrends\Errors\NetworkError;
use Maximeb97\GoogleTrends\Errors\ParseError;
use Maximeb97\GoogleTrends\Errors\UnknownError;

$result = GoogleTrends::dailyTrends(['geo' => 'US']);

if (isset($result['error'])) {
    $error = $result['error'];
    
    if ($error instanceof RateLimitError) {
        echo "Rate limit exceeded. Please try again later.\n";
    } elseif ($error instanceof NetworkError) {
        echo "Network error: " . $error->getMessage() . "\n";
    } elseif ($error instanceof ParseError) {
        echo "Failed to parse response: " . $error->getMessage() . "\n";
    } else {
        echo "Error: " . $error->getMessage() . "\n";
    }
    
    // Get error code
    echo "Error code: " . $error->getErrorCode() . "\n";
    
    // Get status code (if available)
    if ($error->getStatusCode()) {
        echo "HTTP status: " . $error->getStatusCode() . "\n";
    }
}
```

## Object-Oriented Usage

You can also use the `GoogleTrendsApi` class directly:

```php
use Maximeb97\GoogleTrends\GoogleTrendsApi;

$api = new GoogleTrendsApi();
$result = $api->dailyTrends(['geo' => 'US']);

if (isset($result['data'])) {
    // Process data
}
```

## Data Structures

### TrendingStory

```php
class TrendingStory {
    public string $title;
    public string $traffic;
    public ?TrendingImage $image;
    public array $articles;  // TrendingArticle[]
    public string $shareUrl;
}
```

### TrendingArticle

```php
class TrendingArticle {
    public string $title;
    public string $url;
    public string $source;
    public string $time;
    public string $snippet;
}
```

### TrendingImage

```php
class TrendingImage {
    public string $newsUrl;
    public string $source;
    public string $imageUrl;
}
```

## Time Formats

The library supports various time formats for the `time` parameter:

- `'now 1-d'` - Past day
- `'now 7-d'` - Past 7 days
- `'today 1-m'` - Past month
- `'today 3-m'` - Past 3 months
- `'today 12-m'` - Past 12 months
- `'today 5-y'` - Past 5 years
- `'all'` - All available data (2004-present)
- Custom: `'YYYY-MM-DD YYYY-MM-DD'` - Specific date range

## License

MIT

## Credits

This is a PHP port of [@shaivpidadi/trends-js](https://github.com/Shaivpidadi/trends-js).

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.
