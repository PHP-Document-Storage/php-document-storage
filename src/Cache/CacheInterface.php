<?php

declare(strict_types=1);

namespace Devnix\PhpDocumentStore\Cache;

interface CacheInterface
{
    /**
     * @param non-empty-string $suffix
     */
    public function set(DocumentInterface $document, string|null $suffix = null): void;

    /**
     * @template TDocument of DocumentInterface
     *
     * @param class-string<TDocument> $document
     * @param TDocument|null          $default
     * @param non-empty-string|null   $suffix
     *
     * @return TDocument|null
     * @phpstan-return ($default is null ? TDocument|null : TDocument)
     */
    public function get(string $document, string|null $suffix = null, DocumentInterface $default = null): DocumentInterface|null;
}
