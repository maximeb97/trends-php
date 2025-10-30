<?php

/**
 * Example usage of the Google Trends PHP library
 */

// Simple autoloader for testing without Composer
spl_autoload_register(function ($class) {
    $prefix = 'Maximeb97\\GoogleTrends\\';
    $base_dir = __DIR__ . '/src/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

use Maximeb97\GoogleTrends\GoogleTrends;
use Maximeb97\GoogleTrends\Types\GoogleTrendsTrendingHours;

echo "=== Google Trends PHP Library Examples ===\n\n";

// Example 1: Daily Trends
echo "1. Daily Trends (US):\n";
$result = GoogleTrends::dailyTrends(['geo' => 'US', 'lang' => 'en']);

if (isset($result['data'])) {
    $trends = $result['data'];
    echo "Found " . count($trends->allTrendingStories) . " trending stories\n";
    
    // Display first 3 stories
    for ($i = 0; $i < min(3, count($trends->allTrendingStories)); $i++) {
        $story = $trends->allTrendingStories[$i];
        echo "  " . ($i + 1) . ". " . $story->title . " (Traffic: " . $story->traffic . ")\n";
    }
} else {
    echo "Error: " . $result['error']->getMessage() . "\n";
}

echo "\n";

// Example 2: Real-Time Trends
echo "2. Real-Time Trends (US, 4 hours):\n";
$result = GoogleTrends::realTimeTrends([
    'geo' => 'US',
    'trendingHours' => GoogleTrendsTrendingHours::FOUR_HOURS
]);

if (isset($result['data'])) {
    $trends = $result['data'];
    echo "Found " . count($trends->allTrendingStories) . " trending stories\n";
    
    // Display first 3 stories
    for ($i = 0; $i < min(3, count($trends->allTrendingStories)); $i++) {
        $story = $trends->allTrendingStories[$i];
        echo "  " . ($i + 1) . ". " . $story->title . " (Traffic: " . $story->traffic . ")\n";
    }
} else {
    echo "Error: " . $result['error']->getMessage() . "\n";
}

echo "\n";

// Example 3: Autocomplete
echo "3. Autocomplete suggestions for 'bitcoin':\n";
$result = GoogleTrends::autocomplete('bitcoin', 'en-US');

if (isset($result['data'])) {
    $suggestions = $result['data'];
    echo "Found " . count($suggestions) . " suggestions\n";
    
    for ($i = 0; $i < min(5, count($suggestions)); $i++) {
        echo "  " . ($i + 1) . ". " . $suggestions[$i] . "\n";
    }
} else {
    echo "Error: " . $result['error']->getMessage() . "\n";
}

echo "\n";

// Example 4: Explore
echo "4. Explore 'bitcoin':\n";
$result = GoogleTrends::explore([
    'keyword' => 'bitcoin',
    'geo' => 'US',
    'time' => 'today 12-m'
]);

if (isset($result['widgets'])) {
    echo "Found " . count($result['widgets']) . " widgets\n";
    
    foreach ($result['widgets'] as $widget) {
        if (isset($widget['id'])) {
            echo "  - Widget: " . $widget['id'] . "\n";
        }
    }
} else {
    echo "Error: " . $result['error']->getMessage() . "\n";
}

echo "\n";

// Example 5: Related Queries
echo "5. Related queries for 'bitcoin':\n";
$result = GoogleTrends::relatedQueries([
    'keyword' => 'bitcoin',
    'geo' => 'US',
    'time' => 'now 1-d'
]);

if (isset($result['data'])) {
    $rankedList = $result['data']['default']['rankedList'];
    if (!empty($rankedList)) {
        echo "Top related queries:\n";
        $keywords = $rankedList[0]['rankedKeyword'] ?? [];
        for ($i = 0; $i < min(5, count($keywords)); $i++) {
            echo "  " . ($i + 1) . ". " . $keywords[$i]['query'] . " (" . $keywords[$i]['formattedValue'] . ")\n";
        }
    }
} else {
    echo "Error: " . $result['error']->getMessage() . "\n";
}

echo "\n";

// Example 6: Related Data (Topics + Queries)
echo "6. Related data for 'bitcoin':\n";
$result = GoogleTrends::relatedData([
    'keyword' => 'bitcoin',
    'geo' => 'US',
    'time' => 'now 1-d'
]);

if (isset($result['data'])) {
    echo "Related Topics:\n";
    $topics = $result['data']['topics'] ?? [];
    for ($i = 0; $i < min(3, count($topics)); $i++) {
        echo "  " . ($i + 1) . ". " . $topics[$i]['topic']['title'] . "\n";
    }
    
    echo "\nRelated Queries:\n";
    $queries = $result['data']['queries'] ?? [];
    for ($i = 0; $i < min(3, count($queries)); $i++) {
        echo "  " . ($i + 1) . ". " . $queries[$i]['query'] . "\n";
    }
} else {
    echo "Error: " . $result['error']->getMessage() . "\n";
}

echo "\n=== Examples Complete ===\n";
