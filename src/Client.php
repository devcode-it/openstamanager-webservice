<?php

namespace OSM\API;

use UnexpectedValueException;

if (!function_exists('ends_with')) {
    /**
     * Check if a string ends with the given string.
     *
     * @param string $string
     * @param string $ends_with
     *
     * @return bool
     */
    function ends_with($string, $ends_with)
    {
        return substr($string, -strlen($ends_with)) === $ends_with;
    }
}

class Client
{
    /** @var string OSM URL */
    protected $url;

    /** @var string Authentification key */
    protected $token;

    /** @var bool is debug activated */
    protected $debug;

    /** @var GuzzleHttp\Client */
    protected $client;

    /**
     * Throw an exception when CURL is not installed/activated.
     *
     * @param string $url   Root URL for the shop
     * @param string $token Authentification key
     * @param mixed  $debug Debug mode Activated (true) or deactivated (false)
     */
    public function __construct($url, $token = null, $debug = true)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL, ['flags' => FILTER_FLAG_HOST_REQUIRED])) {
            throw new UnexpectedValueException();
        }

        if (!ends_with($url, '/api') && !ends_with($url, '/api/')) {
            $url = $url.'/api/';
        } elseif (ends_with($url, '/api')) {
            $url = $url.'/';
        }

        $this->url = $url;
        $this->token = $token;        
        $this->debug = $debug;

        $this->client = new \GuzzleHttp\Client([
            'base_uri' => $url,
        ]);
    }

    public function login($username, $password)
    {
        $response = $this->create('login', [
            'username' => $username,
            'password' => $password,
        ]);

        if (!empty($response['token'])) {
            $this->token = $response['token'];
        }
    }

    public function logout()
    {
        $this->create('logout');
    }

    public function retrieve($resource, $data = [])
    {
        return $this->request($resource, $data, 'GET');
    }

    public function create($resource, $data = [])
    {
        return $this->request($resource, $data, 'POST');
    }

    public function update($resource, $data = [])
    {
        return $this->request($resource, $data, 'PUT');
    }

    public function delete($resource, $data = [])
    {
        return $this->request($resource, $data, 'DELETE');
    }

    public function request($resource, $data = [], $type = 'GET')
    {
        $json = $data;
        $json['token'] = $this->token;
        $json['resource'] = $resource;

        $request = [
            'json' => $json,
            'http_errors' => false,
        ];

        $response = $this->client->request(strtoupper($type), '', $request);

        $body = (string) $response->getBody();
        $array = json_decode($body, true);

        $result = !empty($array) ? $array : $body;

        return $result;
    }
}
