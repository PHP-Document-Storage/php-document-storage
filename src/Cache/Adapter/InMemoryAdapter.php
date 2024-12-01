<?php

declare(strict_types=1);

namespace Devnix\DocumentStore\Cache\Adapter;

use Devnix\DocumentStore\Cache\DocumentInterface;

final class InMemoryAdapter implements AdapterInterface
{
    /**
     * @var array<non-empty-string, DocumentInterface>
     */
    private array $documents = [];

    public function __construct()
    {
    }

    public function set(string $key, DocumentInterface $document): void
    {
        $this->documents[$key] = $document;
    }

    public function get(string $key): ?DocumentInterface
    {
        if (!$this->exists($key)) {
            return null;
        }

        return $this->documents[$key];
    }

    /**
     * @param non-empty-string $key
     */
    public function exists(string $key): bool
    {
        return array_key_exists($key, $this->documents);
    }
}
