<?php

declare(strict_types=1);

namespace Devnix\DocumentStore\Cache;

use function Devnix\DocumentStore\Internal\suffix;

final class Cache implements CacheInterface
{
    public function __construct(private Adapter\AdapterInterface $adapter)
    {
    }

    public function set(DocumentInterface $document, ?string $suffix = null): void
    {
        $key = $document::identifier().suffix($suffix);

        $this->adapter->set($key, $document);
    }

    public function get(string $documentClassName, ?string $suffix = null): DocumentInterface
    {
        $document = $this->tryGet($documentClassName, $suffix);

        if (null === $document) {
            $key = $documentClassName::identifier().suffix($suffix);

            throw new Exception\DocumentNotFoundException(sprintf('Could not find a document with key "%s" for class "%s"', $key, $documentClassName));
        }

        return $document;
    }

    public function tryGet(string $documentClassName, ?string $suffix = null, ?DocumentInterface $default = null): ?DocumentInterface
    {
        $key = $documentClassName::identifier().suffix($suffix);

        // @phpstan-ignore return.type
        return $this->adapter->get($key) ?? $default;
    }

    public function exists(string $documentClassName, ?string $suffix = null): bool
    {
        $key = $documentClassName::identifier().suffix($suffix);

        return $this->adapter->exists($key);
    }

    public function tryUpdate(string $documentClassName, \Closure $callback, ?string $suffix = null, ?DocumentInterface $default = null): ?DocumentInterface
    {
        $document = $this->tryGet($documentClassName, $suffix, $default);

        if (null !== $document) {
            $document = $callback($document);
            $this->set($document, $suffix);
        }

        return $document;
    }

    public function update(string $documentClassName, \Closure $callback, ?string $suffix = null, ?DocumentInterface $default = null): DocumentInterface
    {
        $document = $this->tryUpdate($documentClassName, $callback, $suffix, $default);

        if (null === $document) {
            $key = $documentClassName::identifier().suffix($suffix);

            throw new Exception\DocumentNotFoundException(sprintf('Could not find a document with key "%s" for class "%s"', $key, $documentClassName));
        }

        return $document;
    }
}
