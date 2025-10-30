<?php

namespace Maximeb97\GoogleTrends\Errors;

class RateLimitError extends GoogleTrendsError
{
    public function __construct(string $message = 'Rate limit exceeded', $details = null)
    {
        parent::__construct($message, 'RATE_LIMIT_EXCEEDED', 429, $details);
    }
}
