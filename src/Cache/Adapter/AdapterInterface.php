<?php

declare(strict_types=1);

namespace Devnix\PhpDocumentStore\Cache\Adapter;

use Devnix\PhpDocumentStore\Cache\DocumentInterface;

interface AdapterInterface
{
    /**
     * @param non-empty-string $key
     */
    public function set(string $key, DocumentInterface $document): void;

    /**
     * @param non-empty-string $key
     */
    public function get(string $key): DocumentInterface|null;
}