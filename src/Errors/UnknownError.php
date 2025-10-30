<?php

namespace Maximeb97\GoogleTrends\Errors;

class UnknownError extends GoogleTrendsError
{
    public function __construct(string $message = 'An unknown error occurred', $details = null)
    {
        parent::__construct($message, 'UNKNOWN_ERROR', null, $details);
    }
}
