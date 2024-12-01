<?php

declare(strict_types=1);

namespace Devnix\DocumentStore\Cache\Adapter;

use Devnix\DocumentStore\Cache\DocumentInterface;

interface AdapterInterface
{
    /**
     * @param non-empty-string $key
     */
    public function set(string $key, DocumentInterface $document): void;

    /**
     * @param non-empty-string $key
     */
    public function get(string $key): ?DocumentInterface;
}
