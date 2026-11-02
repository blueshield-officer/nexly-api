<?php

namespace App\Factory;

use Symfony\Component\RateLimiter\LimiterInterface;
use Symfony\Component\RateLimiter\RateLimiterFactoryInterface;

readonly class RateLimiterServiceFactory
{
    public function __construct(){}

    public function create(
        RateLimiterFactoryInterface $limiterFactory,
        int $userId
    ) : LimiterInterface
    {
        return $limiterFactory->create($userId);
    }
}
