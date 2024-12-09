<?php

/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 7/9/20, 3:50 PM
 * Copyright (c) 2024. Powered by iamir.net
 */

namespace iAmirNet\Curl;

/**
 * Class iCurlService
 * A service class to interact with cURL requests with customizable options for HTTP methods.
 */
class iCurlService
{
    /**
     * @var string $base The base URL for the API.
     * @var array $headers The headers to be used in the requests.
     * @var array $options Additional options to configure the cURL request.
     * @var bool $just_response Determines if only the response data is returned.
     */
    public $base, $headers, $options;
    public $just_response = true;

    /**
     * iCurlService constructor.
     * Initializes the service with the base URL, headers, and options.
     * @param string $base The base URL for the API.
     * @param array $headers Headers to be included in requests.
     * @param array $options cURL options for requests.
     */
    public function __construct($base, $headers = [], $options = [])
    {
        $this->base = $base;
        $this->headers = $headers;
        $this->options = $options;
    }

    /**
     * Sets whether only the response data should be returned (true) or the full result (false).
     * @param bool $value The value to set (true for just response, false for full result).
     * @return $this Returns the current instance for chaining.
     */
    public function justResponse(bool $value = true)
    {
        $this->just_response = $value;
        return $this;
    }

    /**
     * Makes an HTTP request using the specified method and options.
     * @param string $endpoint The endpoint for the request (relative to base URL).
     * @param array $params The query parameters to include in the request.
     * @param mixed $data The data to send in the request body (for POST/PUT).
     * @param array $headers Additional headers to add to the request.
     * @param string $method The HTTP method to use (GET, POST, PUT, DELETE, etc.).
     * @param array $options Additional cURL options.
     * @param bool $is_json Whether the response should be automatically decoded as JSON.
     * @return mixed The response data or full response depending on `just_response` flag.
     */
    public function request(string $endpoint = '/', array $params = [], $data = null, array $headers = [], string $method = 'GET', array $options = [], $is_json = true)
    {
        foreach ($this->options as $index => $option)
            $options[$index] = $option;
        $result = iCurl::request($this->base, $endpoint, $params, $data, array_merge($this->headers, $headers), $method, $options, $is_json);
        return $this->just_response && isset($result['response']) ? $result['response'] : $result;
    }

    /**
     * Makes a GET request to the given endpoint.
     * @param string $endpoint The endpoint for the request (relative to base URL).
     * @param array $params Query parameters for the request.
     * @param array $headers Additional headers to include.
     * @param array $options cURL options to customize the request.
     * @return mixed The response data or full response depending on `just_response` flag.
     */
    public function get(string $endpoint = '/', array $params = [], array $headers = [], array $options = [])
    {
        return $this->request($endpoint, $params, null, $headers, 'GET', $options);
    }

    /**
     * Makes a POST request to the given endpoint with the provided data.
     * @param string $endpoint The endpoint for the request (relative to base URL).
     * @param mixed $data The data to send in the request body.
     * @param array $headers Additional headers to include.
     * @param array $options cURL options to customize the request.
     * @return mixed The response data or full response depending on `just_response` flag.
     */
    public function post(string $endpoint = '/', $data = null, array $headers = [], array $options = [])
    {
        return $this->request($endpoint, [], $data, $headers, 'POST', $options);
    }

    /**
     * Makes a DELETE request to the given endpoint with optional data and parameters.
     * @param string $endpoint The endpoint for the request (relative to base URL).
     * @param array $params Query parameters for the request.
     * @param mixed $data Data to send in the request body (optional).
     * @param array $headers Additional headers to include.
     * @param array $options cURL options to customize the request.
     * @return mixed The response data or full response depending on `just_response` flag.
     */
    public function delete(string $endpoint = '/', array $params = [], $data = null, array $headers = [], array $options = [])
    {
        return $this->request($endpoint, $params, $data, $headers, 'DELETE', $options);
    }

    /**
     * Makes a PUT request to the given endpoint with the provided data.
     * @param string $endpoint The endpoint for the request (relative to base URL).
     * @param array $params Query parameters for the request.
     * @param mixed $data The data to send in the request body.
     * @param array $headers Additional headers to include.
     * @param array $options cURL options to customize the request.
     * @return mixed The response data or full response depending on `just_response` flag.
     */
    public function put(string $endpoint = '/', array $params = [], $data = null, array $headers = [], array $options = [])
    {
        return $this->request($endpoint, $params, $data, $headers, 'PUT', $options);
    }
}
