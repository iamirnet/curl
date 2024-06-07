<?php
/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 7/9/20, 3:50 PM
 * Copyright (c) 2024. Powered by iamir.net
 */

namespace iAmirNet\Curl;

class iCurl
{
    private static function endpoint(string $url, array $params = []): string
    {
        return count($params) ? ("{$url}?" . http_build_query($params, '', '&')) : $url;
    }
    
    private static function init(string $url, array $headers, array $options)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt_array($curl, $options);
        return $curl;
    }

    private static function execute($curl, string $url)
    {
        $output = curl_exec($curl);
        if (curl_errno($curl))
            throw new \Exception(curl_error($curl));
        curl_close($curl);
        return static::json_decode($output);
    }

    public static function post(string $url, array $params = [], $data = null, array $headers = [], array $options = [])
    {
        $curl = static::init(static::endpoint($url, $params), $headers, $options);
        curl_setopt($curl, CURLOPT_POST, true);
        if ($data) curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        return static::execute($curl, $url);
    }

    public static function get(string $url, array $params = [], array $headers = [], array $options = [])
    {
        return static::execute(static::init(static::endpoint($url, $params), $headers, $options), $url);
    }
    
    public static function delete(string $url, array $params = [], $data = [], array $headers = [], array $options = [])
    {
        return static::other('DELETE', ...func_num_args());
    }

    
    public static function put(string $url, array $params = [], $data = [], array $headers = [], array $options = [])
    {
        return static::other('PUT', ...func_num_args());
    }

    public static function other(string $method, string $url, array $params = [], $data = [], array $headers = [], array $options = [])
    {
        $curl = static::init(static::endpoint($url, $params), $headers, $options);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        if ($data) curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        return static::execute($curl, $url);
    }

    public static function json_decode($string)
    {
        if (!$string || is_array($string))
            return $string;
        $output = json_decode($string, true);
        return (json_last_error() === JSON_ERROR_NONE) ? $output : $string;
    }

    public static function request(string $base, string $url, array $params = [], $data = null, array $headers = [], string $method = 'GET', array $options = [])
    {
        $formattedHeaders = [];
        foreach ($headers as $index => $header)
            $formattedHeaders[] = "{$index}: {$header}";
        $endpoint = "{$base}{$url}";
        switch (strtoupper($method)) {
            case 'GET':
                return static::get($endpoint, $params, $formattedHeaders, $options);
            case 'POST':
                return static::post($endpoint, $params, $data, $formattedHeaders, $options);
            case 'DELETE':
                return static::delete($endpoint, $params, $data, $formattedHeaders, $options);
            case 'PUT':
                return static::put($endpoint, $params, $data, $formattedHeaders, $options);
            default:
                return static::other($method, $endpoint, $params, $data, $formattedHeaders, $options);
        }
    }
}
