<?php
/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 7/9/20, 3:50 PM
 * Copyright (c) 2024. Powered by iamir.net
 */

namespace iAmirNet\Curl;

class iCurl
{
    /**
     * Converts a raw HTTP header string into an associative array.
     *
     * @param string $header The raw HTTP header string.
     * @return array The headers as an associative array, including the HTTP status.
     */
    public static function headers2Array($header)
    {
        $headers = array();
        $lines = explode("\r\n", $header);
        $headers['http_status'] = array_shift($lines);
        foreach ($lines as $line) {
            if (!empty($line)) {
                list($key, $value) = explode(': ', $line, 2);
                $headers[$key] = $value;
            }
        }
        return $headers;
    }

    /**
     * Constructs a complete URL with optional query parameters.
     *
     * @param string $url The base URL.
     * @param array $params Query parameters as an associative array.
     * @return string The full URL with appended query parameters.
     */
    private static function endpoint(string $url, array $params = []): string
    {
        return count($params) ? ("{$url}?" . http_build_query($params, '', '&')) : $url;
    }

    /**
     * Initializes a cURL session with common settings.
     *
     * @param string $url The request URL.
     * @param array $headers HTTP headers for the request.
     * @param array $options Additional cURL options.
     * @return resource The initialized cURL handle.
     */
    private static function init(string $url, array $headers, array $options)
    {
        $curl = curl_init();
        unset($options['CURl_I_TYPE']);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt_array($curl, $options);
        return $curl;
    }

    /**
     * Executes a cURL request and processes the response.
     *
     * @param resource $curl The cURL handle.
     * @param array $request_headers HTTP headers used for the request.
     * @param array $options Additional options used for the request.
     * @return array Response details, including status, headers, and body.
     */
    private static function execute($curl, $request_headers, $options = [])
    {
        $response = curl_exec($curl);
        $request_info = curl_getinfo($curl);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $headers = static::headers2Array(substr($response, 0, $header_size));
        $output = substr($response, $header_size);

        if (@$headers['content-encoding'] == "gzip")
            $output = zlib_decode($output);

        $result['response_headers'] = $headers;
        $result['request_info'] = $request_info;
        $result['request_headers'] = $request_headers;
        $result['options'] = $options;

        if (curl_errno($curl))
            return array_merge(['status' => false, 'code' => -100, 'message' => curl_error($curl)], $result);
        elseif (($http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE)) !== 200)
            return array_merge(['status' => false, 'code' => "h_" . $http_code, 'message' => curl_error($curl), 'response' => $output], $result);

        curl_close($curl);
        return array_merge(['status' => true, 'code' => 200, 'message' => 'The operation was successful.', 'response' => $output], $result);
    }

    /**
     * Sends a POST request.
     *
     * @param string $url The request URL.
     * @param array $params Query parameters.
     * @param mixed $data The data to send in the POST body.
     * @param array $headers HTTP headers.
     * @param array $options Additional cURL options.
     * @return array Response details, including status and response body.
     */
    public static function post(string $url, array $params = [], $data = null, array $headers = [], array $options = [])
    {
        $curl = static::init(static::endpoint($url, $params), $headers, $options);
        curl_setopt($curl, CURLOPT_POST, true);
        if ($data) curl_setopt($curl, CURLOPT_POSTFIELDS, static::convert_data($headers, $data));
        return static::execute($curl, $headers, $options);
    }

    /**
     * Sends a GET request.
     *
     * @param string $url The request URL.
     * @param array $params Query parameters.
     * @param array $headers HTTP headers.
     * @param array $options Additional cURL options.
     * @return array Response details, including status and response body.
     */
    public static function get(string $url, array $params = [], array $headers = [], array $options = [])
    {
        return static::execute(static::init(static::endpoint($url, $params), $headers, $options), $headers, $options);
    }

    /**
     * Sends a DELETE request.
     *
     * @param string $url The request URL.
     * @param array $params Query parameters.
     * @param mixed $data Data to include in the request body.
     * @param array $headers HTTP headers.
     * @param array $options Additional cURL options.
     * @return array Response details, including status and response body.
     */
    public static function delete(string $url, array $params = [], $data = [], array $headers = [], array $options = [])
    {
        return static::other('DELETE', ...func_get_args());
    }

    /**
     * Sends a PUT request.
     *
     * @param string $url The request URL.
     * @param array $params Query parameters.
     * @param mixed $data Data to include in the request body.
     * @param array $headers HTTP headers.
     * @param array $options Additional cURL options.
     * @return array Response details, including status and response body.
     */
    public static function put(string $url, array $params = [], $data = [], array $headers = [], array $options = [])
    {
        return static::other('PUT', ...func_get_args());
    }

    /**
     * Sends a request with a custom HTTP method.
     *
     * @param string $method The HTTP method (e.g., PATCH, OPTIONS).
     * @param string $url The request URL.
     * @param array $params Query parameters.
     * @param mixed $data Data to include in the request body.
     * @param array $headers HTTP headers.
     * @param array $options Additional cURL options.
     * @return array Response details, including status and response body.
     */
    public static function other(string $method, string $url, array $params = [], $data = [], array $headers = [], array $options = [])
    {
        $curl = static::init(static::endpoint($url, $params), $headers, $options);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        if ($data) curl_setopt($curl, CURLOPT_POSTFIELDS, static::convert_data($headers, $data));
        return static::execute($curl, $headers, $options);
    }

    /**
     * Downloads a file from the given URL and saves it to the specified path.
     *
     * @param string $base The base URL.
     * @param string $url The endpoint to fetch the file from.
     * @param string $out Path to save the downloaded file.
     * @param array $params Query parameters.
     * @param mixed $data Data to include in the request body.
     * @param array $headers HTTP headers.
     * @param string $method HTTP method to use (default: GET).
     * @param array $options Additional cURL options.
     * @return array Status of the operation and storage location.
     */
    public static function download(string $base, string $url, string $out, array $params = [], $data = null, array $headers = [], string $method = 'GET', array $options = [])
    {
        $fp = fopen($out, 'w+');
        $options_all = [
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_COOKIEFILE => '',
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5',
            CURLOPT_FILE => $fp,
            'CURl_I_TYPE' => "FILE",
        ];
        foreach ($options as $index => $option)
            $options_all[$index] = $option;
        $result = static::request($base, $url, $params, $data, $headers, $method, $options_all, false);
        if (is_array($result) && isset($result['status']) && !$result['status']) {
            if (file_exists($out)) unlink($out);
            return $result;
        }
        fclose($fp);
        return ['status' => true, 'storage' => $out];
    }

    /**
     * Checks if a resource exists at the given URL.
     *
     * @param string $url The URL to check.
     * @return bool True if the resource exists, false otherwise.
     */
    public static function exists(string $url): bool
    {
        $file_headers = @get_headers($url);
        return !(!$file_headers || strpos($file_headers[0], '404') !== false);
    }

    /**
     * Decodes a JSON response into an associative array.
     *
     * @param mixed $response The JSON string or data to decode.
     * @return mixed The decoded array or the original response if decoding fails.
     */
    public static function json_decode($response)
    {
        $output = json_decode($response, true);
        return (json_last_error() === JSON_ERROR_NONE) ? $output : $response;
    }

    /**
     * Checks if a given string is valid JSON.
     *
     * @param mixed $string The string to check.
     * @return bool True if the string is valid JSON, false otherwise.
     */
    public static function is_json($string)
    {
        if (is_array($string))
            return false;
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * Converts data to a format suitable for sending based on the content type.
     *
     * @param array $headers HTTP headers.
     * @param mixed $data Data to convert.
     * @return mixed The formatted data.
     */
    public static function convert_data($headers, $data)
    {
        $content_type = static::get_content_type($headers);
        if (static::is_content_type_json($headers, $content_type))
            return is_array($data) || is_object($data) ? json_encode((array)$data) : $data;
        if (static::is_content_type_url($headers, $content_type))
            return is_array($data) || is_object($data) ? http_build_query((array)$data) : $data;
        return $data;
    }

    /**
     * Checks if the content type is JSON.
     *
     * @param array $headers HTTP headers.
     * @param string|null $content_type Optional explicit content type.
     * @return bool True if the content type is JSON, false otherwise.
     */
    public static function is_content_type_json($headers, $content_type = null)
    {
        return static::is_content_type($headers, 'json', $content_type);
    }

    /**
     * Checks if the content type is URL-encoded.
     *
     * @param array $headers HTTP headers.
     * @param string|null $content_type Optional explicit content type.
     * @return bool True if the content type is URL-encoded, false otherwise.
     */
    public static function is_content_type_url($headers, $content_type = null)
    {
        return static::is_content_type($headers, 'x-www-form-urlencoded', $content_type);
    }

    /**
     * Checks if the content type matches a given type.
     *
     * @param array $headers HTTP headers.
     * @param string $type The type to check (e.g., JSON, URL-encoded).
     * @param string|null $content_type Optional explicit content type.
     * @return bool True if the content type matches, false otherwise.
     */
    public static function is_content_type($headers, $type, $content_type = null)
    {
        $content_type = ($content_type ?: static::get_content_type($headers)) ?: '';
        return strpos($content_type, $type) !== false || $content_type == $type;
    }

    /**
     * Extracts the content type from headers.
     *
     * @param array $headers HTTP headers.
     * @return string|false The content type or false if not found.
     */
    public static function get_content_type($headers)
    {
        foreach ($headers as $header)
            if (strpos(strtolower($header), 'content-type') !== false)
                try {
                    return trim(explode(':', $header)[1]);
                } catch (\Throwable $exception) {
                    return $header;
                }
        return false;
    }

    /**
     * Sends an HTTP request using the specified method and parameters.
     *
     * @param string $base The base URL.
     * @param string $url The endpoint URL.
     * @param array $params Query parameters.
     * @param mixed $data Data to send in the request body.
     * @param array $headers HTTP headers.
     * @param string $method HTTP method (GET, POST, etc.).
     * @param array $options Additional cURL options.
     * @param bool $is_json Whether to decode the response as JSON.
     * @return array The response details, including status and body.
     */
    public static function request(string $base, string $url, array $params = [], $data = null, array $headers = [], string $method = 'GET', array $options = [], $is_json = true)
    {
        $formattedHeaders = [];
        foreach ($headers as $index => $header)
            $formattedHeaders[] = "{$index}: {$header}";
        $endpoint = "{$base}{$url}";
        switch (strtoupper($method)) {
            case 'GET':
                $result = static::get($endpoint, $params, $formattedHeaders, $options);
                break;
            case 'POST':
                $result = static::post($endpoint, $params, $data, $formattedHeaders, $options);
                break;
            case 'DELETE':
                $result = static::delete($endpoint, $params, $data, $formattedHeaders, $options);
                break;
            case 'PUT':
                $result = static::put($endpoint, $params, $data, $formattedHeaders, $options);
                break;
            default:
                $result = static::other($method, $endpoint, $params, $data, $formattedHeaders, $options);
                break;
        }
        if ($is_json && isset($result['response']))
            $result['response'] = static::json_decode($result['response']);
        return $result;
    }


    public static function proxy($url)
    {
        $parsed = parse_url($url);
        if (!isset($parsed['scheme'], $parsed['host'], $parsed['port']))
            return false;

        $scheme = strtolower($parsed['scheme']);
        $host = $parsed['host'];
        $port = $parsed['port'];

        $address = "{$host}:{$port}";
        switch ($scheme) {
            case 'http':
            case 'https':
                $type = CURLPROXY_HTTP;
                break;
            case 'socks':
            case 'socks5':
                $type = CURLPROXY_SOCKS5;
                break;
            case 'socks4':
                $type = CURLPROXY_SOCKS4;
                break;
            default:
                return false;
        }
        return [$type, $address];
    }
}
