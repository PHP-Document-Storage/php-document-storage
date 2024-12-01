<?php

declare(strict_types=1);

namespace Devnix\DocumentStore\Cache;

use Devnix\DocumentStore\Cache\CacheInterface;
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

    public function get(string $document, ?string $suffix = null, DocumentInterface $default = null): DocumentInterface|null
    {
        $key = $document::identifier() .suffix($suffix);

        // @phpstan-ignore return.type
        return $this->adapter->get($key) ?? $default;
    }
}