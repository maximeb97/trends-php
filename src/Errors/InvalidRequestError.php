<?php

namespace Maximeb97\GoogleTrends\Errors;

class InvalidRequestError extends GoogleTrendsError
{
    public function __construct(string $message = 'Invalid request parameters', $details = null)
    {
        parent::__construct($message, 'INVALID_REQUEST', 400, $details);
    }
}
