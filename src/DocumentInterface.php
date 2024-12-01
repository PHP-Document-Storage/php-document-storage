<?php

declare(strict_types=1);

namespace Devnix\DocumentStore;

interface DocumentInterface
{
    /**
     * @return non-empty-string
     */
    public static function identifier(): string;
}
