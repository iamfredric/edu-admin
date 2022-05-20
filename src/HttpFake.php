<?php

namespace Iamfredric\EduAdmin;

use Iamfredric\EduAdmin\Contracts\HttpClient;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Assert as PHPUnit;

class HttpFake implements HttpClient
{
    /**
     * @var array<string, array<int|string, mixed>>
     */
    protected array $record = [];

    /**
     * @var array<string,array<string,mixed>>
     */
    protected array $when = [
        'token' => [
            '.issued' => '2022-05-19 14:31:00',
            'expires_in' => 200,
            'access_token' => 'fake-token'
        ]
    ];

    public function __construct(?callable $responses = null)
    {
        if ($responses) {
            (new Collection($responses()))
                ->each(fn ($response, $uri) => $this->when($uri, $response));
        }
    }

    /**
     * @param string $url
     * @param array<array<string,mixed>> $response
     * @return $this
     */
    public function when(string $url, array $response): static
    {
        $this->when[$this->trimUri($url)] = $response;

        return $this;
    }

    /**
     * @param string $url
     * @param array<string,mixed> $params
     * @param array<string,mixed> $headers
     * @return Collection
     */
    public function get(string $url, array $params = [], array $headers = []): Collection
    {
        return $this->record('get', $url, $params, $headers);
    }

    /**
     * @param string $url
     * @param array<string,mixed> $params
     * @param array<string,mixed> $headers
     * @return Collection
     */
    public function post(string $url, array $params = [], array $headers = []): Collection
    {
        return $this->record('post', $url, $params, $headers);
    }

    /**
     * @param string $url
     * @param array<string,mixed> $params
     * @param array<string,mixed> $headers
     * @return Collection
     */
    public function put(string $url, array $params = [], array $headers = []): Collection
    {
        return $this->record('put', $url, $params, $headers);
    }

    /**
     * @param string $url
     * @param array<string,mixed> $params
     * @param array<string,mixed> $headers
     * @return Collection
     */
    public function patch(string $url, array $params = [], array $headers = []): Collection
    {
        return $this->record('patch', $url, $params, $headers);
    }

    /**
     * @param string $method
     * @param string $url
     * @param array<string,mixed> $params
     * @param array<string,mixed> $headers
     * @return Collection
     */
    protected function record(string $method, string $url, array $params = [], array $headers = [])
    {
        $url = $this->trimUri($url);

        if (! isset($this->record[$url])) {
            $this->record[$url] = [];
        }

        $this->record[$url][] = compact('method', 'params', 'headers');

        return new Collection($this->when[$url] ?? []);
    }

    public function recorded(string $url): Collection
    {
        return new Collection($this->record[$this->trimUri($url)] ?? []);
    }

    public function assertCalled(string $url): static
    {
        PHPUnit::assertTrue(
            $this->recorded($url)->count() > 0,
            "The request to {$url} was not recorded, endpoints recorded: ".implode(', ', array_keys($this->record))
        );

        return $this;
    }

    private function trimUri(string $url): string
    {
        return trim(str_replace(Client::BASE_URL, '', $url), '/');
    }
}
