<?php

namespace Maximeb97\GoogleTrends\Errors;

class ParseError extends GoogleTrendsError
{
    public function __construct(string $message = 'Failed to parse response', $details = null)
    {
        parent::__construct($message, 'PARSE_ERROR', null, $details);
    }
}
