<?php

namespace Iamfredric\EduAdmin;

use Carbon\Carbon;
use Exception;
use Iamfredric\EduAdmin\Contracts\HttpClient;
use Iamfredric\EduAdmin\Exceptions\MissingClientCredentialsException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class Client
{
    const BASE_URL = 'https://api.eduadmin.se';

    protected static ?string $username = null;

    protected static ?string $password = null;

    protected static ?HttpClient $client = null;

    public function __construct()
    {
        if (empty(static::$username) || empty(static::$password)) {
            throw new MissingClientCredentialsException(
                'Client cannot be instantiated without credentials'
            );
        }
    }

    public static function setCredentials(
        string $username,
        string $password
    ): void {
        static::$username = $username;
        static::$password = $password;
    }

    public static function setClient(HttpClient $client): void
    {
        static::$client = $client;
    }

    /**
     * @param string $uri
     * @param array<string, mixed> $attributes
     * @return Collection
     */
    public function put(string $uri, array $attributes): Collection
    {
        return $this->getClient()->put(
            "https://api.eduadmin.se/v1/{$uri}",
            $attributes,
            ['Authorization' => 'Bearer ' . $this->token()]
        );
    }

    /**
     * @param string $uri
     * @param array<string, mixed> $attributes
     * @return Collection
     */
    public function post(string $uri, array $attributes): Collection
    {
        return $this->getClient()->post(
            "https://api.eduadmin.se/v1/{$uri}",
            $attributes,
            ['Authorization' => 'Bearer ' . $this->token()]
        );
    }

    /**
     * @param string $uri
     * @param array<string, mixed> $attributes
     * @return Collection
     */
    public function get(string $uri, array $attributes): Collection
    {
        return $this->getClient()->get(
            "https://api.eduadmin.se/v1/{$uri}",
            $attributes,
            ['Authorization' => 'Bearer ' . $this->token()]
        );
    }

    /**
     * @param string $uri
     * @param array<string, mixed> $attributes
     * @return Collection
     */
    public function delete(string $uri, array $attributes = []): Collection
    {
        return $this->getClient()->delete(
            "https://api.eduadmin.se/v1/{$uri}",
            $attributes,
            ['Authorization' => 'Bearer ' . $this->token()]
        );
    }

    protected function token(): ?string
    {
        return $this->credentials()->get('access_token');
    }

    protected function credentials(): Collection
    {
        $response = new Collection();

        if (!Cache::has('edu-admin.credentials')) {
            $response = $this->authorize();

            Cache::put(
                'edu-admin.credentials',
                $response,
                Carbon::parse($response->get('.issued'))->addSeconds(
                    $response->get('expires_in')
                )
            );
        }

        return Cache::get('edu-admin.credentials', $response);
    }

    protected function authorize(): Collection
    {
        $response = $this->getClient()->post(
            'https://api.eduadmin.se/token',
            [
                'username' => static::$username,
                'password' => static::$password,
                'grant_type' => 'password',
            ],
            [
                'content-type' => 'application/x-www-form-urlencoded',
            ]
        );

        if (!$response->has('.issued', 'expires_in', 'access_token')) {
            throw new Exception('Unexpected response from EduAdmin');
        }

        return $response;
    }

    protected function getClient(): HttpClient
    {
        return static::$client ??= new \Iamfredric\EduAdmin\HttpClient();
    }

    public static function fake(?callable $responses = null): HttpFake
    {
        static::setClient($fake = new HttpFake($responses));
        static::setCredentials('fake-user', 'fake-password');

        return $fake;
    }
}
