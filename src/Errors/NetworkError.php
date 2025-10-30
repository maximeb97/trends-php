<?php

namespace Maximeb97\GoogleTrends\Errors;

class NetworkError extends GoogleTrendsError
{
    public function __construct(string $message = 'Network request failed', $details = null)
    {
        parent::__construct($message, 'NETWORK_ERROR', null, $details);
    }
}
