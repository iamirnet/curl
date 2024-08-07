<?php
/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 7/9/20, 3:50 PM
 * Copyright (c) 2024. Powered by iamir.net
 */

namespace iAmirNet\Curl;

class iCurl
{
    public static function headers2Array($header) {
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

    private static function endpoint(string $url, array $params = []): string
    {
        return count($params) ? ("{$url}?" . http_build_query($params, '', '&')) : $url;
    }

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

    private static function execute($curl, $options = [])
    {
        $response = curl_exec($curl);
        $request_info = curl_getinfo($curl);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $headers = static::headers2Array(substr($response, 0, $header_size));
        $output = substr($response, $header_size);
        if (@$headers['content-encoding'] == "gzip")
            $output = zlib_decode($output);
        if (curl_errno($curl))
            return ['status' => false, 'code' => -100, 'message' => curl_error($curl)];
        elseif(($http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE)) !== 200)
            return ['status' => false, 'code' => "h_".$http_code, 'message' => curl_error($curl)];
        curl_close($curl);
        return [$output, $headers, $request_info, $options];
    }

    public static function post(string $url, array $params = [], $data = null, array $headers = [], array $options = [])
    {
        $curl = static::init(static::endpoint($url, $params), $headers, $options);
        curl_setopt($curl, CURLOPT_POST, true);
        if ($data) curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        return static::execute($curl, $options);
    }

    public static function get(string $url, array $params = [], array $headers = [], array $options = [])
    {
        return static::execute(static::init(static::endpoint($url, $params), $headers, $options), $options);
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
        return static::execute($curl, $options);
    }

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

    public static function exists(string $url): bool
    {
        $file_headers = @get_headers($url);
        return !(!$file_headers || strpos($file_headers[0], '404') !== false);
    }

    public static function json_decode($response)
    {
        list($string, $headers, $info, $options) = $response;
        if (!$string || is_array($string))
            return $string;
        $output = json_decode($string, true);
        return ['status' => true, 'headers' => $headers, 'request' => $info, 'result' => (json_last_error() === JSON_ERROR_NONE) ? $output : $string];
    }

    public static function request(string $base, string $url, array $params = [], $data = null, array $headers = [], string $method = 'GET', array $options = [], $json = true)
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
                $result =  static::post($endpoint, $params, $data, $formattedHeaders, $options);
                break;
            case 'DELETE':
                $result =  static::delete($endpoint, $params, $data, $formattedHeaders, $options);
                break;
            case 'PUT':
                $result =  static::put($endpoint, $params, $data, $formattedHeaders, $options);
                break;
            default:
                $result =  static::other($method, $endpoint, $params, $data, $formattedHeaders, $options);
                break;
        }
        return $json ? static::json_decode($result) : $result;
    }
}
