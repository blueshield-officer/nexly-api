<?php

namespace App\Service;

use Doctrine\DBAL\Connection;

readonly class HealthChecker
{
    public function __construct(
        private Connection   $connection,
        private RedisService $redis,
    )
    {
    }

    /** @return array<string, bool> */
    public function check(): array
    {
        return [
            'database' => $this->run(fn () => $this->connection->executeQuery('SELECT 1')),
            'redis' => $this->run(fn () => $this->redis->ping()),
        ];
    }

    private function run(callable $probe): bool
    {
        try {
            $probe();

            return true;
        } catch (\Throwable) {
            return false;
        }
    }
}
