<?php

namespace App\Service;

use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\RateLimiter\LimiterInterface;
use Symfony\Component\RateLimiter\RateLimiterFactoryInterface;

class RateLimiterService
{
    public function __construct(
        private LimiterInterface $limiter,
    ){}

    public function consume()
    {
        if ($this->limiter->consume()->isAccepted() === false) {
            throw new TooManyRequestsHttpException();
        }
    }
}
