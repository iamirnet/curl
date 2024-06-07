<?php

/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 7/9/20, 3:50 PM
 * Copyright (c) 2024. Powered by iamir.net
 */

if ( !function_exists('i_curl'))
{
    function i_curl(string $base, string $url, array $params = [], $data = null, array $headers = [], string $method = 'GET', array $options = [])
    {
        return \iAmirNet\Curl\iCurl::request($base, $url, $params, $data, $headers, $method, $options);
    }
}

if ( !function_exists('i_curl_get'))
{
    function i_curl_get(string $base, string $url, array $params = [], array $headers = [], array $options = [])
    {
        return \iAmirNet\Curl\iCurl::request($base, $url, $params, null, $headers, "GET", $options);
    }
}

if ( !function_exists('i_curl_post'))
{
    function i_curl_post(string $base, string $url, $data = null, array $params = [], array $headers = [], array $options = [])
    {
        return \iAmirNet\Curl\iCurl::request($base, $url, $params, $data, $headers, "POST", $options);
    }
}

if ( !function_exists('i_curl_delete'))
{
    function i_curl_delete(string $base, string $url, $data = null, array $params = [], array $headers = [], array $options = [])
    {
        return \iAmirNet\Curl\iCurl::request($base, $url, $params, $data, $headers, "DELETE", $options);
    }
}

if ( !function_exists('i_curl_put'))
{
    function i_curl_put(string $base, string $url, $data = null, array $params = [], array $headers = [], array $options = [])
    {
        return \iAmirNet\Curl\iCurl::request($base, $url, $params, $data, $headers, "PUT", $options);
    }
}