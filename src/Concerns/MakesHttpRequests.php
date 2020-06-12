<?php

namespace Weble\JoomlaTestBench\Concerns;

use Joomla\Uri\Uri;
use Psr\Http\Message\UploadedFileInterface;
use Weble\JoomlaTestBench\Input\TestInput;
use Weble\JoomlaTestBench\Response\TestResponse;

trait MakesHttpRequests
{
    /**
     * Additional headers for the request.
     *
     * @var array
     */
    protected $defaultHeaders = [];

    /**
     * Additional cookies for the request.
     *
     * @var array
     */
    protected $defaultCookies = [];

    /**
     * Additional server variables for the request.
     *
     * @var array
     */
    protected $serverVariables = [];

    /**
     * Indicates whether redirects should be followed.
     *
     * @var bool
     */
    protected $followRedirects = false;

    /**
     * @var string|null
     */
    protected $previousUrl;

    /**
     * Define additional headers to be sent with the request.
     *
     * @param array $headers
     * @return $this
     */
    public function withHeaders(array $headers): self
    {
        $this->defaultHeaders = array_merge($this->defaultHeaders, $headers);

        return $this;
    }

    /**
     * Add a header to be sent with the request.
     *
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function withHeader(string $name, string $value): self
    {
        $this->defaultHeaders[$name] = $value;

        return $this;
    }

    /**
     * Flush all the configured headers.
     *
     * @return $this
     */
    public function flushHeaders(): self
    {
        $this->defaultHeaders = [];

        return $this;
    }

    /**
     * Define a set of server variables to be sent with the requests.
     *
     * @param array $server
     * @return $this
     */
    public function withServerVariables(array $server): self
    {
        $this->serverVariables = $server;

        return $this;
    }

    /**
     * Define additional cookies to be sent with the request.
     *
     * @param array $cookies
     * @return $this
     */
    public function withCookies(array $cookies): self
    {
        $this->defaultCookies = array_merge($this->defaultCookies, $cookies);

        return $this;
    }

    /**
     * Add a cookie to be sent with the request.
     *
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function withCookie(string $name, string $value): self
    {
        $this->defaultCookies[$name] = $value;

        return $this;
    }

    /**
     * Automatically follow any redirects returned from the response.
     *
     * @return $this
     */
    public function followingRedirects(): self
    {
        $this->followRedirects = true;

        return $this;
    }

    /**
     * Set the referer header and previous URL session value in order to simulate a previous request.
     *
     * @param string $url
     * @return $this
     */
    public function from(string $url): self
    {
        $this->previousUrl = $url;

        return $this->withHeader('referer', $url);
    }

    /**
     * Visit the given URI with a GET request.
     *
     * @param string $uri
     * @param array $headers
     * @return TestResponse
     */
    public function get($uri, array $headers = []): TestResponse
    {
        $server  = $this->transformHeadersToServerVars($headers);
        $cookies = $this->defaultCookies;

        return $this->call('GET', $uri, [], $cookies, [], $server);
    }

    /**
     * Visit the given URI with a GET request, expecting a JSON response.
     *
     * @param string $uri
     * @param array $headers
     * @return TestResponse
     */
    public function getJson($uri, array $headers = []): TestResponse
    {
        return $this->json('GET', $uri, [], $headers);
    }

    /**
     * Visit the given URI with a POST request.
     *
     * @param string $uri
     * @param array $data
     * @param array $headers
     * @return TestResponse
     */
    public function post($uri, array $data = [], array $headers = []): TestResponse
    {
        $server  = $this->transformHeadersToServerVars($headers);
        $cookies = $this->defaultCookies;

        return $this->call('POST', $uri, $data, $cookies, [], $server);
    }

    /**
     * Visit the given URI with a POST request, expecting a JSON response.
     *
     * @param string $uri
     * @param array $data
     * @param array $headers
     * @return TestResponse
     */
    public function postJson($uri, array $data = [], array $headers = []): TestResponse
    {
        return $this->json('POST', $uri, $data, $headers);
    }

    /**
     * Visit the given URI with a PUT request.
     *
     * @param string $uri
     * @param array $data
     * @param array $headers
     * @return TestResponse
     */
    public function put($uri, array $data = [], array $headers = []): TestResponse
    {
        $server  = $this->transformHeadersToServerVars($headers);
        $cookies = $this->defaultCookies;

        return $this->call('PUT', $uri, $data, $cookies, [], $server);
    }

    /**
     * Visit the given URI with a PUT request, expecting a JSON response.
     *
     * @param string $uri
     * @param array $data
     * @param array $headers
     * @return TestResponse
     */
    public function putJson($uri, array $data = [], array $headers = []): TestResponse
    {
        return $this->json('PUT', $uri, $data, $headers);
    }

    /**
     * Visit the given URI with a PATCH request.
     *
     * @param string $uri
     * @param array $data
     * @param array $headers
     * @return TestResponse
     */
    public function patch($uri, array $data = [], array $headers = []): TestResponse
    {
        $server  = $this->transformHeadersToServerVars($headers);
        $cookies = $this->defaultCookies;

        return $this->call('PATCH', $uri, $data, $cookies, [], $server);
    }

    /**
     * Visit the given URI with a PATCH request, expecting a JSON response.
     *
     * @param string $uri
     * @param array $data
     * @param array $headers
     * @return TestResponse
     */
    public function patchJson($uri, array $data = [], array $headers = []): TestResponse
    {
        return $this->json('PATCH', $uri, $data, $headers);
    }

    /**
     * Visit the given URI with a DELETE request.
     *
     * @param string $uri
     * @param array $data
     * @param array $headers
     * @return TestResponse
     */
    public function delete($uri, array $data = [], array $headers = []): TestResponse
    {
        $server  = $this->transformHeadersToServerVars($headers);
        $cookies = $this->defaultCookies;

        return $this->call('DELETE', $uri, $data, $cookies, [], $server);
    }

    /**
     * Visit the given URI with a DELETE request, expecting a JSON response.
     *
     * @param string $uri
     * @param array $data
     * @param array $headers
     * @return TestResponse
     */
    public function deleteJson($uri, array $data = [], array $headers = []): TestResponse
    {
        return $this->json('DELETE', $uri, $data, $headers);
    }

    public function options($uri, array $data = [], array $headers = []): TestResponse
    {
        $server  = $this->transformHeadersToServerVars($headers);
        $cookies = $this->defaultCookies;

        return $this->call('OPTIONS', $uri, $data, $cookies, [], $server);
    }

    public function optionsJson($uri, array $data = [], array $headers = []): TestResponse
    {
        return $this->json('OPTIONS', $uri, $data, $headers);
    }

    public function json($method, $uri, array $data = [], array $headers = []): TestResponse
    {
        $files = $this->extractFilesFromDataArray($data);

        $content = json_encode($data);

        $headers = array_merge([
            'CONTENT_LENGTH' => mb_strlen($content, '8bit'),
            'CONTENT_TYPE'   => 'application/json',
            'Accept'         => 'application/json',
        ], $headers);

        return $this->call(
            $method,
            $uri,
            [],
            [],
            $files,
            $headers,
            $content
        );
    }

    protected function call(string $method, string $url, array $parameters = [], $cookies = [], $files = [], $server = [], $content = null): TestResponse
    {
        $_SERVER['REQUEST_METHOD'] = $method;

        $uri = $this->prepareUrlForRequest($url);

        $files = array_merge($files, $this->extractFilesFromDataArray($parameters));

        $data  = array_merge($uri->getQuery(true), $parameters);
        $input = new TestInput([
            'FILES'   => $files,
            'COOKIES' => $cookies,
            'SERVER'  => $server,
            'BODY'    => $content,
            'REQUEST' => $data,
            'GET'     => $uri->getQuery(true),
            'POST'    => (strtoupper($method) === 'POST') ? $parameters : [],
        ]);

        $this->createApplication()->setInput($input);
        $this->createApplication()->execute();

        return $this->createApplication()->getResponse();
    }

    protected function prepareUrlForRequest(string $uri): Uri
    {
        if ($uri[0] === '/') {
            $uri = substr($uri, 1);
        }

        $url = 'https://127.0.0.1/' . $uri;
        $uri = new Uri($url);

        $_SERVER['REQUEST_URI'] = $url;
        $_SERVER['HTTPS']       = true;
        $_SERVER['PATH']        = $uri->getPath();
        $_SERVER['HTTP_HOST']   = '';

        return $uri;
    }

    protected function extractFilesFromDataArray(&$data)
    {
        $files = [];

        foreach ($data as $key => $value) {
            if ($value instanceof UploadedFileInterface) {
                $files[$key] = $value;

                unset($data[$key]);
            }

            if (is_array($value)) {
                $files[$key] = $this->extractFilesFromDataArray($value);

                $data[$key] = $value;
            }
        }

        return $files;
    }

    /**
     * Transform headers array to array of $_SERVER vars with HTTP_* format.
     *
     * @param array $headers
     * @return array
     */
    protected function transformHeadersToServerVars(array $headers): array
    {
        $headers = array_merge($this->defaultHeaders, $headers);

        $returnHeaders = [];
        foreach ($headers as $name => $value) {
            $name                                               = strtr(strtoupper($name), '-', '_');
            $returnHeaders[$this->formatServerHeaderKey($name)] = $value;
        }

        return $returnHeaders;
    }

    /**
     * Format the header name for the server array.
     *
     * @param string $name
     * @return string
     */
    protected function formatServerHeaderKey($name): string
    {
        if (! stripos($name, 'HTTP_') !== 0 && $name !== 'CONTENT_TYPE' && $name !== 'REMOTE_ADDR') {
            return 'HTTP_' . $name;
        }

        return $name;
    }
}
