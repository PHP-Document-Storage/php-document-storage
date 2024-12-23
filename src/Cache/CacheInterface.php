<?php

declare(strict_types=1);

namespace Devnix\DocumentStore\Cache;

interface CacheInterface
{
    /**
     * Write a document to the cache.
     *
     * @param non-empty-string $suffix
     */
    public function set(DocumentInterface $document, ?string $suffix = null): void;

    /**
     * Retrieve a document from the cache.
     *
     * @template TDocument of DocumentInterface
     *
     * @param class-string<TDocument> $documentClassName
     * @param non-empty-string|null   $suffix
     *
     * @return TDocument
     *
     * @throws Exception\DocumentNotFoundException if the document does not exist
     */
    public function get(string $documentClassName, ?string $suffix = null): DocumentInterface;

    /**
     * Tries to retrieve a document from the cache, returns null if the document does not exist.
     *
     * @template TDocument of DocumentInterface
     *
     * @param class-string<TDocument> $documentClassName
     * @param non-empty-string|null   $suffix
     * @param TDocument|null          $default
     *
     * @return TDocument|null
     *
     * @phpstan-return ($default is null ? TDocument|null : TDocument)
     */
    public function tryGet(string $documentClassName, ?string $suffix = null, ?DocumentInterface $default = null): ?DocumentInterface;

    /**
     * @param class-string<DocumentInterface> $documentClassName
     * @param non-empty-string|null           $suffix
     */
    public function exists(string $documentClassName, ?string $suffix = null): bool;

    /**
     * Executes the callback closure and replaces the document with the returned document from the closure.
     * If the document does not exist, the default document will be provided to the callback.
     *
     * @template TDocument of DocumentInterface
     *
     * @param class-string<TDocument>          $documentClassName
     * @param \Closure(TDocument): (TDocument) $callback
     * @param non-empty-string|null            $suffix
     * @param TDocument|null                   $default
     *
     * @psalm-external-mutation-free
     *
     * @return TDocument
     *
     * @throws Exception\DocumentNotFoundException if the document does not exist
     */
    public function update(string $documentClassName, \Closure $callback, ?string $suffix = null, ?DocumentInterface $default = null): DocumentInterface;

    /**
     * If the value exists, executes the callback closure and replaces the value with the returned value from the
     * closure.
     * If the value does not exist and there is not a default value provided, the callback will not be called and the
     * value will remain empty.
     *
     * @template TDocument of DocumentInterface
     *
     * @param class-string<TDocument>          $documentClassName
     * @param \Closure(TDocument): (TDocument) $callback
     * @param non-empty-string|null            $suffix
     * @param TDocument|null                   $default
     *
     * @psalm-external-mutation-free
     *
     * @return TDocument|null
     *
     * @phpstan-return ($default is null ? TDocument|null : TDocument)
     */
    public function tryUpdate(string $documentClassName, \Closure $callback, ?string $suffix = null, ?DocumentInterface $default = null): ?DocumentInterface;
}
