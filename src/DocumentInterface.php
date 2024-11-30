<?php

declare(strict_types=1);

namespace Devnix\PhpDocumentStore;

interface DocumentInterface
{
    /**
     * @return non-empty-string
     */
    public static function identifier(): string;
}