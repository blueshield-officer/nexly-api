<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class InternalServerService
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string              $internalWebsiteUrl
    )
    {}

    public function get(string $path, array $query = []): void
    {
        $this->request('GET', $path, ['query' => $query]);
    }

    public function post(string $path, array $data = []): void
    {
        $this->request('POST', $path, ['json' => $data]);
    }

    private function request(string $method, string $path, array $options = []): void
    {
        try {
            $response = $this->httpClient->request(
                $method,
                rtrim($this->internalWebsiteUrl, '/') . '/api/' . ltrim($path, '/'),
                $options
            );

            $response->getStatusCode();;
        } catch (TransportExceptionInterface $e) {
            throw new \RuntimeException('Internal server unreachable: ' . $e->getMessage(), previous: $e);
        } catch (ClientExceptionInterface|ServerExceptionInterface $e) {
            throw new \RuntimeException('Internal server error: ' . $e->getMessage(), previous: $e);
        }
    }
}
