<?php

namespace App\Scraper\Sat;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class SatPageFetcher
{
    const string URL = 'https://satsuite.collegeboard.org/sat/dates-deadlines';

    public function __construct(
        private readonly HttpClientInterface $client
    ) {}

    public function fetchHtml(): string
    {
        return $this->client
            ->request('GET', self::URL)
            ->getContent();
    }
}
