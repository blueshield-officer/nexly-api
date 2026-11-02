<?php

namespace App\Service;

readonly class RedisService
{
    private const string NULL_VALUE = '__null__';

    public function __construct(
        private \Redis $redis,
        private string $prefix,
        private int    $ttl,
    )
    {
    }

    public function ping(): bool
    {
        return $this->redis->ping() === true || $this->redis->ping() === 'PONG';
    }

    public function get(array $keys): mixed
    {
        $data = $this->redis->get($this->makeKey($keys));
        if ($data === false) return null;

        $unserialized = unserialize($data);
        if ($unserialized === self::NULL_VALUE) return null;

        return $unserialized;
    }

    public function set(array $keys, mixed $value, ?int $ttl = null): void
    {
        if (null === $ttl) $ttl = $this->ttl;
        if (is_null($value)) $value = self::NULL_VALUE;

        $this->redis->setex($this->makeKey($keys), $ttl, serialize($value));
    }

    public function getOrSet(array $keys, callable $callback, ?int $ttl = null): mixed
    {
        if (null === $ttl) $ttl = $this->ttl;

        if ($this->exists($keys)) {
            return $this->get($keys);
        }

        $data = $callback();

        if (is_null($data)) $ttl = 30;

        $this->set($keys, $data, $ttl);

        return $data;
    }

    public function delete(array $keys): void
    {
        $this->redis->del($this->makeKey($keys));
    }


    public function deleteMany(array $keysArray): void
    {
        foreach ($keysArray as $key) $this->delete($key);
    }

    public function exists(array $keys): bool
    {
        return (bool) $this->redis->exists($this->makeKey($keys));
    }

    private function makeKey(array $keys): string
    {
        return $this->prefix . ":" . join(':', $keys);
    }

}
