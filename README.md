[![Latest Version](https://img.shields.io/github/release/iamirnet/xt.com.svg?style=flat-square)](https://github.com/iamirnet/xt.com/releases)
[![GitHub last commit](https://img.shields.io/github/last-commit/iamirnet/xt.com.svg?style=flat-square)](#)
[![Packagist Downloads](https://img.shields.io/packagist/dt/iamirnet/curl.svg?style=flat-square)](https://packagist.org/packages/iamirnet/curl)

# iCurl

iCurl is a lightweight PHP library for making HTTP requests using cURL. It supports various HTTP methods, handles JSON and URL-encoded data, and simplifies common cURL operations.

---

## Features

- Easy-to-use interface for HTTP methods: `GET`, `POST`, `PUT`, `DELETE`, and more.
- Automatic handling of query parameters and data formatting.
- Built-in support for file downloads.
- JSON encoding/decoding and validation utilities.
- Flexible configuration with additional cURL options.

---
#### Installation
```
composer require iamirnet/curl
```
<details>
 <summary>Click for help with installation</summary>

## Install Composer
If the above step didn't work, install composer and try again.
#### Debian / Ubuntu
```
sudo apt-get install curl php-curl
curl -s http://getcomposer.org/installer | php
php composer.phar install
```
Composer not found? Use this command instead:
```
php composer.phar require "iamirnet/curl"
```

#### Installing on Windows
Download and install composer:
1. https://getcomposer.org/download/
2. Create a folder on your drive like C:\iAmirNet\XT
3. Run command prompt and type `cd C:\iAmirNet\XT`
4. ```composer require iamirnet/curl```
5. Once complete copy the vendor folder into your project.

</details>

#### Getting started
`composer require iamirnet/curl`
```php
require 'vendor/autoload.php';
// config by specifying api key and secret
$service = new iAmirNet\Curl\iCurlService('https://api.example.com', ['Authorization' => 'Bearer token']);
```



---

## Usage
### Creating an iCurlService Instance

```php
$service = new iAmirNet\Curl\iCurlService('https://api.example.com', ['Authorization' => 'Bearer token']);
```

### Sending a GET Request

```php
$response = $service->get('/endpoint', ['param1' => 'value1']);
echo $response;
```

### Sending a POST Request

```php
$data = ['key' => 'value'];
$response = $service->post('/endpoint', $data);
echo $response;
```

### Sending a PUT Request

```php
$data = ['key' => 'newValue'];
$response = $service->put('/endpoint', [], $data);
echo $response;
```

### Sending a DELETE Request

```php
$response = $service->delete('/endpoint');
echo $response;
```

---
### Usage Static

#### Sending a GET Request

```php
$response = iAmirNet\Curl\iCurl::get('https://api.example.com/data', ['key' => 'value'], ['Authorization' => 'Bearer token']);
if ($response['status']) {
    print_r($response['response']);
} else {
    echo "Error: " . $response['message'];
}
```

#### Sending a POST Request

```php
$data = ['name' => 'John Doe', 'email' => 'john@example.com'];
$response = iAmirNet\Curl\iCurl::post('https://api.example.com/users', [], $data, ['Content-Type' => 'application/json']);
if ($response['status']) {
    echo "User created successfully!";
} else {
    echo "Error: " . $response['message'];
}
```

#### Sending a PUT Request

```php
$data = ['name' => 'Jane Doe'];
$response = iAmirNet\Curl\iCurl::put('https://api.example.com/users/1', [], $data, ['Content-Type' => 'application/json']);
if ($response['status']) {
    echo "User updated successfully!";
} else {
    echo "Error: " . $response['message'];
}
```

#### Sending a DELETE Request

```php
$response = iAmirNet\Curl\iCurl::delete('https://api.example.com/users/1', [], [], ['Authorization' => 'Bearer token']);
if ($response['status']) {
    echo "User deleted successfully!";
} else {
    echo "Error: " . $response['message'];
}
```

---

### Advanced Features

#### Downloading Files

```php
$response = iAmirNet\Curl\iCurl::download(
    'https://files.example.com',
    '/path/to/file.zip',
    '/local/storage/file.zip',
    [],
    null,
    ['Authorization' => 'Bearer token'],
    'GET'
);

if ($response['status']) {
    echo "File downloaded to: " . $response['storage'];
} else {
    echo "Download failed: " . $response['message'];
}
```

#### Checking Resource Existence

```php
$url = 'https://example.com/resource';
if (iAmirNet\Curl\iCurl::exists($url)) {
    echo "Resource exists!";
} else {
    echo "Resource does not exist.";
}
```

#### Handling JSON

##### Decoding JSON Responses

```php
$jsonString = '{"name":"John", "age":30}';
$decoded = iAmirNet\Curl\iCurl::json_decode($jsonString);
print_r($decoded);
```

##### Validating JSON Strings

```php
$jsonString = '{"name":"John", "age":30}';
if (iAmirNet\Curl\iCurl::is_json($jsonString)) {
    echo "Valid JSON!";
} else {
    echo "Invalid JSON!";
}
```

---
# iCurl Functions

This package provides helper functions for interacting with the iCurl class. These functions make it easy to send HTTP requests (GET, POST, PUT, DELETE) and check if a URL exists.

---

## Functions Overview

### `i_curl`

This function sends an HTTP request to the specified endpoint using the specified method.

```php
i_curl(string $base, string $url, array $params = [], $data = null, array $headers = [], string $method = 'GET', array $options = [])
```

**Parameters:**
- `string $base`: The base URL for the request.
- `string $url`: The specific URL endpoint for the request.
- `array $params`: Optional query parameters to send with the request.
- `$data`: Optional data to send in the request body.
- `array $headers`: Optional headers to include in the request.
- `string $method`: The HTTP method (default is 'GET').
- `array $options`: Additional curl options.

**Returns:**
- The response from the `iCurl::request` function.

---

### `i_curl_get`

This function sends a GET request to the specified endpoint.

```php
i_curl_get(string $base, string $url, array $params = [], array $headers = [], array $options = [])
```

**Parameters:**
- `string $base`: The base URL for the request.
- `string $url`: The specific URL endpoint for the GET request.
- `array $params`: Optional query parameters to send with the GET request.
- `array $headers`: Optional headers to include in the request.
- `array $options`: Additional curl options.

**Returns:**
- The response from the `iCurl::request` function.

---

### `i_curl_post`

This function sends a POST request to the specified endpoint.

```php
i_curl_post(string $base, string $url, $data = null, array $params = [], array $headers = [], array $options = [])
```

**Parameters:**
- `string $base`: The base URL for the request.
- `string $url`: The specific URL endpoint for the POST request.
- `$data`: Data to send in the POST request body.
- `array $params`: Optional query parameters to send with the POST request.
- `array $headers`: Optional headers to include in the request.
- `array $options`: Additional curl options.

**Returns:**
- The response from the `iCurl::request` function.

---

### `i_curl_delete`

This function sends a DELETE request to the specified endpoint.

```php
i_curl_delete(string $base, string $url, $data = null, array $params = [], array $headers = [], array $options = [])
```

**Parameters:**
- `string $base`: The base URL for the request.
- `string $url`: The specific URL endpoint for the DELETE request.
- `$data`: Optional data to send in the DELETE request body.
- `array $params`: Optional query parameters to send with the DELETE request.
- `array $headers`: Optional headers to include in the request.
- `array $options`: Additional curl options.

**Returns:**
- The response from the `iCurl::request` function.

---

### `i_curl_put`

This function sends a PUT request to the specified endpoint.

```php
i_curl_put(string $base, string $url, $data = null, array $params = [], array $headers = [], array $options = [])
```

**Parameters:**
- `string $base`: The base URL for the request.
- `string $url`: The specific URL endpoint for the PUT request.
- `$data`: Data to send in the PUT request body.
- `array $params`: Optional query parameters to send with the PUT request.
- `array $headers`: Optional headers to include in the request.
- `array $options`: Additional curl options.

**Returns:**
- The response from the `iCurl::request` function.

---

### `i_curl_dl`

This function downloads a file from the specified URL.

```php
i_curl_dl(string $base, string $url, string $out, array $params = [], $data = null, array $headers = [], string $method = 'GET', array $options = [])
```

**Parameters:**
- `string $base`: The base URL for the request.
- `string $url`: The specific URL endpoint to download the file from.
- `string $out`: The local path to save the downloaded file.
- `array $params`: Optional query parameters to send with the request.
- `$data`: Optional data to send with the request.
- `array $headers`: Optional headers to include in the request.
- `string $method`: The HTTP method for the request (default is 'GET').
- `array $options`: Additional curl options.

**Returns:**
- The response from the `iCurl::download` function.

---

### `i_curl_exists`

This function checks if a URL exists by sending a HEAD request.

```php
i_curl_exists(string $base, string $url = '')
```

**Parameters:**
- `string $base`: The base URL for the request.
- `string $url`: The specific URL to check for existence.

**Returns:**
- `true` if the URL exists, `false` if it does not.

---

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.
