<?php

namespace Maximeb97\GoogleTrends\Errors;

use Exception;

class GoogleTrendsError extends Exception
{
    protected string $errorCode;
    protected ?int $statusCode;
    protected $details;

    public function __construct(string $message = '', string $errorCode = 'UNKNOWN_ERROR', ?int $statusCode = null, $details = null)
    {
        parent::__construct($message);
        $this->errorCode = $errorCode;
        $this->statusCode = $statusCode;
        $this->details = $details;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    public function getDetails()
    {
        return $this->details;
    }
}
