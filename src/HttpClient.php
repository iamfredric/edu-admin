<?php

namespace Iamfredric\EduAdmin;

use Iamfredric\EduAdmin\Contracts\HttpClient as HttpClientContract;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class HttpClient implements HttpClientContract
{
    /**
     * @param string $url
     * @param array<string,mixed> $params
     * @param array<string,mixed> $headers
     * @return Collection
     */
    public function get(string $url, array $params = [], array $headers = []): Collection
    {
        return Http::withHeaders($headers)
            ->get($url, $params)
            ->collect();
    }

    /**
     * @param string $url
     * @param array<string,mixed> $params
     * @param array<string,mixed> $headers
     * @return Collection
     */
    public function post(string $url, array $params = [], array $headers = []): Collection
    {
        return Http::withHeaders($headers)
            ->post($url, $params)
            ->collect();
    }

    /**
     * @param string $url
     * @param array<string,mixed> $params
     * @param array<string,mixed> $headers
     * @return Collection
     */
    public function put(string $url, array $params = [], array $headers = []): Collection
    {
        return Http::withHeaders($headers)
            ->put($url, $params)
            ->collect();
    }

    /**
     * @param string $url
     * @param array<string,mixed> $params
     * @param array<string,mixed> $headers
     * @return Collection
     */
    public function patch(string $url, array $params = [], array $headers = []): Collection
    {
        return Http::withHeaders($headers)
            ->patch($url, $params)
            ->collect();
    }
}
