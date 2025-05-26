<?php

/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 7/9/20, 3:50 PM
 * Copyright (c) 2024. Powered by iamir.net
 */

// Check if the 'i_curl' function already exists before declaring it
if ( !function_exists('i_curl'))
{
    /**
     * Sends a general HTTP request using the iCurl class.
     *
     * This function acts as a wrapper to the iCurl::request method.
     *
     * @param string $base The base URL for the request.
     * @param string $url The specific endpoint of the URL.
     * @param array $params Optional query parameters.
     * @param mixed $data Optional data to send with the request (for POST, PUT, DELETE).
     * @param array $headers Optional headers to include in the request.
     * @param string $method The HTTP method to use (default is 'GET').
     * @param array $options Additional curl options to pass.
     *
     * @return mixed The response from the iCurl::request function.
     */
    function i_curl(string $base, string $url, array $params = [], $data = null, array $headers = [], string $method = 'GET', array $options = [])
    {
        return \iAmirNet\Curl\iCurl::request($base, $url, $params, $data, $headers, $method, $options);
    }
}

// Check if the 'i_curl_get' function already exists before declaring it
if ( !function_exists('i_curl_get'))
{
    /**
     * Sends a GET request using the iCurl class.
     *
     * This function is a shortcut for making GET requests.
     *
     * @param string $base The base URL for the request.
     * @param string $url The specific endpoint of the URL.
     * @param array $params Optional query parameters.
     * @param array $headers Optional headers to include in the request.
     * @param array $options Additional curl options to pass.
     *
     * @return mixed The response from the iCurl::request function.
     */
    function i_curl_get(string $base, string $url, array $params = [], array $headers = [], array $options = [])
    {
        return \iAmirNet\Curl\iCurl::request($base, $url, $params, null, $headers, "GET", $options);
    }
}

// Check if the 'i_curl_post' function already exists before declaring it
if ( !function_exists('i_curl_post'))
{
    /**
     * Sends a POST request using the iCurl class.
     *
     * This function is a shortcut for making POST requests.
     *
     * @param string $base The base URL for the request.
     * @param string $url The specific endpoint of the URL.
     * @param mixed $data Data to send in the body of the POST request.
     * @param array $params Optional query parameters.
     * @param array $headers Optional headers to include in the request.
     * @param array $options Additional curl options to pass.
     *
     * @return mixed The response from the iCurl::request function.
     */
    function i_curl_post(string $base, string $url, $data = null, array $params = [], array $headers = [], array $options = [])
    {
        return \iAmirNet\Curl\iCurl::request($base, $url, $params, $data, $headers, "POST", $options);
    }
}

// Check if the 'i_curl_delete' function already exists before declaring it
if ( !function_exists('i_curl_delete'))
{
    /**
     * Sends a DELETE request using the iCurl class.
     *
     * This function is a shortcut for making DELETE requests.
     *
     * @param string $base The base URL for the request.
     * @param string $url The specific endpoint of the URL.
     * @param mixed $data Optional data to send in the DELETE request.
     * @param array $params Optional query parameters.
     * @param array $headers Optional headers to include in the request.
     * @param array $options Additional curl options to pass.
     *
     * @return mixed The response from the iCurl::request function.
     */
    function i_curl_delete(string $base, string $url, $data = null, array $params = [], array $headers = [], array $options = [])
    {
        return \iAmirNet\Curl\iCurl::request($base, $url, $params, $data, $headers, "DELETE", $options);
    }
}

// Check if the 'i_curl_put' function already exists before declaring it
if ( !function_exists('i_curl_put'))
{
    /**
     * Sends a PUT request using the iCurl class.
     *
     * This function is a shortcut for making PUT requests.
     *
     * @param string $base The base URL for the request.
     * @param string $url The specific endpoint of the URL.
     * @param mixed $data Data to send in the body of the PUT request.
     * @param array $params Optional query parameters.
     * @param array $headers Optional headers to include in the request.
     * @param array $options Additional curl options to pass.
     *
     * @return mixed The response from the iCurl::request function.
     */
    function i_curl_put(string $base, string $url, $data = null, array $params = [], array $headers = [], array $options = [])
    {
        return \iAmirNet\Curl\iCurl::request($base, $url, $params, $data, $headers, "PUT", $options);
    }
}

// Check if the 'i_curl_dl' function already exists before declaring it
if ( !function_exists('i_curl_dl'))
{
    /**
     * Downloads a file from the specified URL.
     *
     * This function is a shortcut for downloading a file using the iCurl::download method.
     *
     * @param string $base The base URL for the request.
     * @param string $url The specific URL to download the file from.
     * @param string $out The local path where the file will be saved.
     * @param array $params Optional query parameters.
     * @param mixed $data Optional data to send with the request.
     * @param array $headers Optional headers to include in the request.
     * @param string $method The HTTP method to use for the request (default is 'GET').
     * @param array $options Additional curl options to pass.
     *
     * @return mixed The response from the iCurl::download function.
     */
    function i_curl_dl(string $base, string $url, string $out, array $params = [], $data = null, array $headers = [], string $method = 'GET', array $options = [])
    {
        return \iAmirNet\Curl\iCurl::download($base, $url, $out, $params, $data, $headers, $method, $options);
    }
}

// Check if the 'i_curl_exists' function already exists before declaring it
if ( !function_exists('i_curl_exists'))
{
    /**
     * Checks if a URL exists.
     *
     * This function sends a HEAD request to check if the URL exists.
     *
     * @param string $base The base URL for the request.
     * @param string $url The specific URL to check for existence.
     *
     * @return bool Returns true if the URL exists, false if it does not.
     */
    function i_curl_exists(string $base, string $url = '')
    {
        return \iAmirNet\Curl\iCurl::exists($base . $url);
    }
}


// Check if the 'i_curl_proxy' function already exists before declaring it
if ( !function_exists('i_curl_proxy'))
{
    function i_curl_proxy(string $url)
    {
        return \iAmirNet\Curl\iCurl::proxy($url);
    }
}


// Check if the 'i_curl_proxy' function already exists before declaring it
if ( !function_exists('i_curl_random_ua'))
{
    function i_curl_random_ua(string $url)
    {
        return \iAmirNet\Curl\iCurl::randomUA($url);
    }
}
