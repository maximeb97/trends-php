<?php

namespace Maximeb97\GoogleTrends\Helpers;

class Request
{
    private static ?string $cookieVal = null;

    /**
     * Execute an HTTP request
     * 
     * @param string $url The URL to request
     * @param array $options Options for the request
     * @return array Response with 'text' callable
     * @throws \Exception On request failure
     */
    public static function request(string $url, array $options = []): array
    {
        $method = $options['method'] ?? 'POST';
        $qs = $options['qs'] ?? [];
        $body = $options['body'] ?? null;
        $headers = $options['headers'] ?? [];
        $contentType = $options['contentType'] ?? 'json';

        // Build query string
        if (!empty($qs)) {
            $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($qs);
        }

        // Prepare body
        $bodyString = '';
        if (is_string($body)) {
            $bodyString = $body;
        } elseif ($contentType === 'form' && is_array($body)) {
            $bodyString = http_build_query($body);
        } elseif (is_array($body)) {
            $bodyString = json_encode($body);
        }

        // Initialize cURL
        $ch = curl_init($url);
        
        // Set request method
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        
        // Set return transfer
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        // Set headers
        $curlHeaders = [];
        foreach ($headers as $key => $value) {
            $curlHeaders[] = "$key: $value";
        }
        
        // Add content type header
        if ($contentType === 'form') {
            $curlHeaders[] = 'Content-Type: application/x-www-form-urlencoded';
        } elseif ($contentType === 'json') {
            $curlHeaders[] = 'Content-Type: application/json';
        }
        
        // Add cookie if exists
        if (self::$cookieVal !== null) {
            curl_setopt($ch, CURLOPT_COOKIE, self::$cookieVal);
        }
        
        // Add body
        if (!empty($bodyString)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyString);
            $curlHeaders[] = 'Content-Length: ' . strlen($bodyString);
        }
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $curlHeaders);
        
        // Handle cookies and headers in response
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        
        // Execute request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        
        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new \Exception("cURL error: $error");
        }
        
        $headerString = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);
        
        curl_close($ch);
        
        // Handle rate limiting (429)
        if ($httpCode === 429) {
            // Extract cookie from response headers
            if (preg_match('/Set-Cookie:\s*([^;\r\n]+)/i', $headerString, $matches)) {
                self::$cookieVal = $matches[1];
                
                // Retry request with cookie
                return self::request($url, $options);
            }
        }
        
        return [
            'text' => function() use ($body) {
                return $body;
            },
            'statusCode' => $httpCode,
        ];
    }

    /**
     * Reset the stored cookie
     */
    public static function resetCookie(): void
    {
        self::$cookieVal = null;
    }
}
