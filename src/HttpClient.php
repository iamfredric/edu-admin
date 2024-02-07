<?php

namespace Iamfredric\EduAdmin;

use Iamfredric\EduAdmin\Contracts\HttpClient as HttpClientContract;
use Iamfredric\EduAdmin\Exceptions\BadResponseException;
use Iamfredric\EduAdmin\Exceptions\ForbiddenException;
use Iamfredric\EduAdmin\Exceptions\UnexpectedResponseException;
use Illuminate\Http\Client\Response;
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
    public function get(
        string $url,
        array $params = [],
        array $headers = []
    ): Collection {
        $response = Http::withHeaders($headers)->get($url, $params);

        if (!$response->successful()) {
            $this->sendFailedResponse($response);
        }

        return $response->collect();
    }

    /**
     * @param string $url
     * @param array<string,mixed> $params
     * @param array<string,mixed> $headers
     * @return Collection
     */
    public function post(
        string $url,
        array $params = [],
        array $headers = []
    ): Collection {
        if ($headers['content-type'] ??
            null == 'application/x-www-form-urlencoded'
        ) {
            $response = Http::asForm()
                ->withHeaders($headers)
                ->post($url, $params);
        } else {
            $response = Http::withHeaders($headers)->post($url, $params);
        }

        if (!$response->successful()) {
            $this->sendFailedResponse($response);
        }

        return $response->collect();
    }

    /**
     * @param string $url
     * @param array<string,mixed> $params
     * @param array<string,mixed> $headers
     * @return Collection
     */
    public function put(
        string $url,
        array $params = [],
        array $headers = []
    ): Collection {
        $response = Http::withHeaders($headers)->put($url, $params);

        if (!$response->successful()) {
            $this->sendFailedResponse($response);
        }

        return $response->collect();
    }

    /**
     * @param string $url
     * @param array<string,mixed> $params
     * @param array<string,mixed> $headers
     * @return Collection
     */
    public function patch(
        string $url,
        array $params = [],
        array $headers = []
    ): Collection {
        $response = Http::withHeaders($headers)->patch($url, $params);

        if (!$response->successful()) {
            $this->sendFailedResponse($response);
        }

        return $response->collect();
    }

    /**
     * @param string $url
     * @param array<string,mixed> $params
     * @param array<string,mixed> $headers
     * @return Collection
     */
    public function delete(
        string $url,
        array $params = [],
        array $headers = []
    ): Collection {
        $response = Http::withHeaders($headers)->delete($url, $params);

        if (!$response->successful()) {
            $this->sendFailedResponse($response);
        }

        return $response->collect();
    }

    protected function sendFailedResponse(Response $response): void
    {
        $data = $response->collect();

        match ($response->status()) {
            400 => throw new BadResponseException(
                $response->body() ?: $data->get('title', 'Bad request'),
                $data->toArray(),
                400
            ),
            403 => throw new ForbiddenException(
                $data->get('title'),
                $data->toArray(),
                403
            ),
            default => throw new UnexpectedResponseException(
                $data->get('title'),
                $data->toArray(),
                $response->status()
            ),
        };
    }
}
