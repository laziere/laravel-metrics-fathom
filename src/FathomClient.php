<?php

namespace JeffersonGoncalves\MetricsFathom;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use JeffersonGoncalves\MetricsFathom\Exceptions\AuthenticationException;
use JeffersonGoncalves\MetricsFathom\Exceptions\FathomException;
use JeffersonGoncalves\MetricsFathom\Exceptions\RateLimitException;

class FathomClient
{
    public function __construct(
        private readonly string $token,
        private readonly string $baseUrl,
    ) {}

    /**
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    public function get(string $endpoint, array $params = []): array
    {
        return $this->request('get', $endpoint, $params);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function post(string $endpoint, array $data = []): array
    {
        return $this->request('post', $endpoint, $data);
    }

    /**
     * @return array<string, mixed>
     */
    public function delete(string $endpoint): array
    {
        return $this->request('delete', $endpoint);
    }

    /**
     * @param  'get'|'post'|'delete'  $method
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function request(string $method, string $endpoint, array $data = []): array
    {
        $this->ensureToken();

        $request = $this->buildRequest();
        $url = rtrim($this->baseUrl, '/')."/{$endpoint}";

        /** @var Response $response */
        $response = match ($method) {
            'get' => $request->get($url, $data),
            'post' => $request->post($url, $data),
            'delete' => $request->delete($url),
        };

        return $this->handleResponse($response);
    }

    private function buildRequest(): PendingRequest
    {
        return Http::withToken($this->token)
            ->accept('application/json');
    }

    private function ensureToken(): void
    {
        if ($this->token === '') {
            throw AuthenticationException::missingToken();
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function handleResponse(Response $response): array
    {
        if ($response->successful()) {
            return $response->json() ?? [];
        }

        if ($response->status() === 401) {
            throw AuthenticationException::invalidToken();
        }

        if ($response->status() === 429) {
            throw RateLimitException::exceeded();
        }

        $error = $response->json('error') ?? $response->body();

        throw FathomException::fromResponse($response->status(), is_string($error) ? $error : (string) json_encode($error));
    }
}
