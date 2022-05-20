<?php

namespace Iamfredric\EduAdmin\Contracts;

use Illuminate\Support\Collection;

interface HttpClient
{
    /**
     * @param string $url
     * @param array<string,mixed> $params
     * @param array<string,mixed> $headers
     * @return Collection
     */
    public function get(string $url, array $params = [], array $headers = []): Collection;

    /**
     * @param string $url
     * @param array<string,mixed> $params
     * @param array<string,mixed> $headers
     * @return Collection
     */
    public function post(string $url, array $params = [], array $headers = []): Collection;

    /**
     * @param string $url
     * @param array<string,mixed> $params
     * @param array<string,mixed> $headers
     * @return Collection
     */
    public function put(string $url, array $params = [], array $headers = []): Collection;

    /**
     * @param string $url
     * @param array<string,mixed> $params
     * @param array<string,mixed> $headers
     * @return Collection
     */
    public function patch(string $url, array $params = [], array $headers = []): Collection;
}
