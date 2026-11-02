<?php

namespace App\Environment;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class EnvironmentChecker
{
    public function __construct(
        #[Autowire(param: 'kernel.environment')]
        private string $environment,
    ) {}

    public function isProd(): bool
    {
        return $this->environment === 'prod';
    }

    public function isDev(): bool
    {
        return $this->environment === 'dev';
    }
}
